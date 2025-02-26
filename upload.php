<?php
session_start();
include 'db.php';

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["prescription"])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["prescription"]["name"]);
    move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file);

    $customer_id = $_SESSION["user_id"];
    $sql = "INSERT INTO prescriptions (customer_id, image_path) VALUES ('$customer_id', '$target_file')";
    
    if ($conn->query($sql)) {
        echo "✅ Prescription uploaded! Processing...";
        header("Location: process_ocr.php?image=" . urlencode($target_file));
        exit();
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="prescription" required>
    <button type="submit">Upload</button>
</form>
