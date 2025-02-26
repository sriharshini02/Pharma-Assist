# Pharma Assist - AI-Powered Pharmacist Assistant

## Project Overview

Pharma Assist is a web-based application designed to assist pharmacists and customers in managing prescriptions efficiently. The system uses OCR (Optical Character Recognition) powered by Google Gemini API to extract medicines and dosages from handwritten prescriptions, allowing customers to easily place orders with their pharmacists.

## Features

- **Customer Portal**: Customers can upload prescriptions and select a pharmacist.
- **Pharmacist Portal**: Pharmacists receive and process orders.
- **AI-Powered OCR**: Extracts medicine names, dosages, and frequencies from prescriptions.
- **Order Management**: Customers can view pending and completed orders.
- **Standalone Software for Pharmacists**: Ensures smooth prescription handling.

---

## Technologies Used

- **Backend**: PHP, MySQL
- **Frontend**: HTML, CSS, Bootstrap, JavaScript
- **AI & OCR**: Google Gemini API
- **Database**: MySQL
- **Image Processing**: OpenCV (Python)

---

## Installation & Setup

### Prerequisites

Ensure you have the following installed on your system:

- PHP (>= 7.4)
- MySQL (>= 5.7)
- Apache Server (XAMPP or WAMP recommended)
- Python (>= 3.8)
- Composer (for PHP dependencies)

### Setup Steps

1. **Clone the Repository**:

   ```sh
   git clone https://github.com/yourusername/pharma-assist.git
   cd pharma-assist
   ```

2. **Setup the Database**:

   - Create the database manually in MySQL:
     ```sql
     CREATE DATABASE pharma_assist;
     ```
   - Run the PHP script to create tables:
     ```sh
     php database_creation.php
     ```
   - Verify the database in MySQL:
     ```sql
     SHOW DATABASES;
     USE pharma_assist;
     SHOW TABLES;
     ```

3. **Setup Python OCR Service**:

   - Install dependencies:
     ```sh
     pip install -r requirements.txt
     ```
   - Create a `.env` file and add:
     ```
     GEMINI_API_KEY=your_google_gemini_api_key
     ```

4. **Run the Application**:

   - Start the Apache and MySQL services in XAMPP/WAMP.
   - Access the app in your browser:
     ```
     http://localhost/pharma-assist
     ```

5. **Processing Prescriptions**:

   - Run the OCR script manually (if needed):
     ```sh
     python extract_medicines.py <prescription_id> <image_path>
     ```

---

## How to Use

### Customer Flow:

1. Register/Login as a customer.
2. Upload a prescription and select a pharmacist.
3. View pending/completed orders.

### Pharmacist Flow:

1. Register/Login as a pharmacist.
2. View received orders and process them.
3. Confirm order completion.

---

## Contributors

- **Sri Harshini Bhupathiraju** - [GitHub](https://github.com/sriharshini02/Pharma-Assist)

---

## License

This project is open-source and available under the [MIT License](LICENSE).
