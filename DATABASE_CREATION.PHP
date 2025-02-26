<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "pharma_assist";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully or already exists.<br>";
} else {
    echo "Error creating database: " . $conn->error . "<br>";
}

// Select database
$conn->select_db($database);

// Create users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('customer','pharmacist') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create pharmacists table
$sql = "CREATE TABLE IF NOT EXISTS pharmacists (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    shop_name VARCHAR(255) NOT NULL,
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Create prescriptions table
$sql = "CREATE TABLE IF NOT EXISTS prescriptions (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    extracted_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id)
)";
$conn->query($sql);

// Create medicines table
$sql = "CREATE TABLE IF NOT EXISTS medicines (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    prescription_id INT(11) NOT NULL,
    medicine_name VARCHAR(255) NOT NULL,
    dosage VARCHAR(50),
    frequency VARCHAR(255),
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id)
)";
$conn->query($sql);

// Create orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11) NOT NULL,
    pharmacist_id INT(11) NOT NULL,
    prescription_id INT(11) NOT NULL,
    status ENUM('pending','confirmed','rejected','completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (pharmacist_id) REFERENCES pharmacists(id),
    FOREIGN KEY (prescription_id) REFERENCES prescriptions(id)
)";
$conn->query($sql);

// Create order_medicines table
$sql = "CREATE TABLE IF NOT EXISTS order_medicines (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    order_id INT(11) NOT NULL,
    medicine_name VARCHAR(255) NOT NULL,
    quantity INT(11) NOT NULL,
    status ENUM('available','unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";
$conn->query($sql);

// Create customer_pharmacist table
$sql = "CREATE TABLE IF NOT EXISTS customer_pharmacist (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    customer_id INT(11) NOT NULL,
    pharmacist_id INT(11) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES users(id),
    FOREIGN KEY (pharmacist_id) REFERENCES pharmacists(id)
)";
$conn->query($sql);

$conn->close();

echo "Database setup completed successfully.";
?>
