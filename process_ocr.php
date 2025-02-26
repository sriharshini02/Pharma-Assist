<?php
session_start();
$image_path = $_GET['image'];

$output = shell_exec("python scripts/ocr_script.py " . escapeshellarg($image_path));

echo "<h2>Extracted Medicines</h2>";
echo nl2br($output);
?>
