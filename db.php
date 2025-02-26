<?php
$host = "localhost";    // XAMPP MySQL host
$user = "root";         // Default XAMPP user
$password = "";         // No password by default
$database = "pharma_assist";  // Your database name

// Create a connection
$conn = new mysqli($host, $user, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
