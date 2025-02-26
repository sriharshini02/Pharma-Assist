<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit();
}

// Redirect based on user role
if ($_SESSION["role"] == "customer") {
    header("Location: customer_dashboard.php");
    exit();
} elseif ($_SESSION["role"] == "pharmacist") {
    header("Location: pharmacist_dashboard.php");
    exit();
}
?>
