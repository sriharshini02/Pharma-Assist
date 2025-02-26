import sys
import os
import google.generativeai as genai
import cv2

# Load API Key
genai.configure(api_key="GEMINI_API_KEY")  # type: ignore


# Preprocess Image
def preprocess_image(image_path):
    image = cv2.imread(image_path, cv2.IMREAD_GRAYSCALE)
    processed_image = cv2.adaptiveThreshold(
        image, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C, cv2.THRESH_BINARY, 11, 2
    )
    processed_path = "processed_" + image_path
    cv2.imwrite(processed_path, processed_image)
    return processed_path


# Upload & Extract Text
def extract_text(image_path):
    file = genai.upload_file(image_path, mime_type="image/jpg")  # type: ignore
    model = genai.GenerativeModel(model_name="gemini-1.5-flash")  # type: ignore
    response = model.start_chat(
        history=[{"role": "user", "parts": [file, "Extract medicine list"]}]
    )
    return response.send_message(
        "Extract medicines details (name, dosage, frequency)"
    ).text


if __name__ == "__main__":
    image_path = sys.argv[1]
    processed_image = preprocess_image(image_path)
    extracted_text = extract_text(processed_image)
    print(extracted_text)
