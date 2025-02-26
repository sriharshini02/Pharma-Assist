import sys
import os
import json
import mysql.connector
import google.generativeai as genai
import cv2
import numpy as np
from dotenv import load_dotenv

# Load API key
load_dotenv()
genai.configure(api_key=os.getenv("GEMINI_API_KEY"))  # type: ignore


def preprocess_image(image_path):
    """Preprocess image to enhance text extraction."""
    image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    processed_image = cv2.adaptiveThreshold(
        image, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2
    )
    processed_path = "processed_" + os.path.basename(image_path)
    cv2.imwrite(processed_path, processed_image)
    return processed_path


def extract_medicines(image_path):
    """Extracts medicine details from a prescription image using Gemini AI."""
    processed_image_path = preprocess_image(image_path)
    file = genai.upload_file(processed_image_path, mime_type="image/jpg")  # type: ignore

    generation_config = {
        "temperature": 1,
        "top_p": 0.95,
        "top_k": 40,
        "max_output_tokens": 8192,
        "response_mime_type": "text/plain",
    }

    model = genai.GenerativeModel(  # type: ignore
        model_name="gemini-2.0-flash",
        generation_config=generation_config,  # type: ignore
        system_instruction="Extract medicine, dosage, frequency of medicines in given prescriptions row-wise, comma-separated. If unable to extract any of the three, mention 'null'.\n\nDo not include any additional information or text.",
    )

    chat_session = model.start_chat(
        history=[
            {
                "role": "user",
                "parts": [
                    file,
                ],
            },
            {
                "role": "model",
                "parts": [
                    "FeSO4, null, A.D.\nAscorbic Acid, 500mg, Once a day",
                ],
            },
        ]
    )
    response = chat_session.send_message(
        [file, "Extract medicines with name, dosage, and frequency"]
    )

    medicines = []
    extracted_text = ""

    if response.text:
        extracted_text = response.text.strip()

        #  Debugging: Print the response from Gemini
        print(" Extracted text from Gemini AI:")
        print(extracted_text)

        lines = extracted_text.split("\n")
        for line in lines:
            line = line.strip()
            if line:  # Skip empty lines
                parts = line.split(", ")
                medicines.append(
                    {"name": parts[0], "dosage": parts[1], "frequency": parts[2]}
                )

    #  Debugging: Print extracted medicine list
    print(" Parsed Medicines List:")
    print(json.dumps(medicines, indent=2))

    return extracted_text, medicines


def store_medicines_in_db(prescription_id, extracted_text, medicines):
    """Stores extracted medicines and updates the database."""
    cursor = None
    db = None
    try:
        # Connect to MySQL database
        db = mysql.connector.connect(
            host="localhost", user="root", password="", database="pharma_assist"
        )
        cursor = db.cursor()

        # Update extracted text in prescriptions table
        cursor.execute(
            "UPDATE prescriptions SET extracted_text = %s WHERE id = %s",
            (extracted_text, prescription_id),
        )
        db.commit()

        # Insert extracted medicines into the database
        for medicine in medicines:
            if medicine["name"] != "null":  # Skip completely empty rows
                cursor.execute(
                    "INSERT INTO medicines (prescription_id, medicine_name, dosage, frequency) VALUES (%s, %s, %s, %s)",
                    (
                        prescription_id,
                        medicine["name"],
                        medicine["dosage"],
                        medicine["frequency"],
                    ),
                )
        db.commit()

        print("Medicines extracted and stored successfully.")

    except mysql.connector.Error as err:
        print(f"Database Error: {err}")

    finally:
        if cursor:
            cursor.close()
        if db:
            db.close()


if __name__ == "__main__":
    # ✅ Get prescription ID and image path from command-line arguments
    prescription_id = sys.argv[1]
    image_path = sys.argv[2]

    # ✅ Extract medicines
    extracted_text, extracted_medicines = extract_medicines(image_path)

    # ✅ Store medicines in MySQL
    store_medicines_in_db(prescription_id, extracted_text, extracted_medicines)
