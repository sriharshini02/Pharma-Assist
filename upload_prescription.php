<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "customer") {
    header("Location: index.php");
    exit();
}

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["prescription"])) {
    $customer_id = $_SESSION["user_id"];
    $pharmacist_id = $_POST["pharmacist_id"]; // Selected pharmacist
    $target_dir = "prescriptions/";
    $file_name = time() . "_" . basename($_FILES["prescription"]["name"]);
    $target_file = $target_dir . $file_name;

    if (move_uploaded_file($_FILES["prescription"]["tmp_name"], $target_file)) {
        // Insert prescription into the database
        $sql = "INSERT INTO prescriptions (customer_id, image_path) VALUES ('$customer_id', '$file_name')";
        if ($conn->query($sql)) {
            $prescription_id = $conn->insert_id; // Get inserted prescription ID

            // Insert order into database
            $sql = "INSERT INTO orders (customer_id, pharmacist_id, prescription_id, status) 
                    VALUES ('$customer_id', '$pharmacist_id', '$prescription_id', 'pending')";
            if ($conn->query($sql)) {
                echo "✅ Prescription uploaded and assigned successfully!";

                $command = "python3 extract_medicines.py $prescription_id $target_file 2>&1";
                exec($command, $output, $return_var);
                echo "<pre>";
                print_r($output);
                echo "</pre>";

                
            } else {
                echo "❌ Error saving order.";
            }
        } else {
            echo "❌ Error saving prescription.";
        }
    } else {
        echo "❌ File upload failed.";
    }
}
?>
<a href="customer_dashboard.php">Back</a>
