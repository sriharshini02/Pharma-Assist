<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $role = $_POST['role']; // 'customer' or 'pharmacist'

    // Insert into users table
    $sql = "INSERT INTO users (name, email, password_hash, role) VALUES ('$name', '$email', '$password', '$role')";
    if ($conn->query($sql)) {
        if ($role === 'pharmacist') {
            // If the user is a pharmacist, insert into pharmacists table
            $pharmacist_name = $name; // Pharmacist name same as user name
            $shop_name = $conn->real_escape_string($_POST['shop_name']);
            $address = $conn->real_escape_string($_POST['address']);
            $phone = $conn->real_escape_string($_POST['phone']);

            $sql = "INSERT INTO pharmacists (name, shop_name, address, phone) 
                    VALUES ('$pharmacist_name', '$shop_name', '$address', '$phone')";
            
            if (!$conn->query($sql)) {
                echo "<div class='alert alert-danger'>❌ Error adding pharmacist details: " . $conn->error . "</div>";
                exit();
            }
        }
        echo "<div class='alert alert-success'>✅ Registered successfully. <a href='index.php'>Login</a></div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            max-width: 500px;
            margin: 50px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .hidden {
            display: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="form-container">
        <h2 class="text-center mb-4">Register</h2>
        <form method="POST">
            <div class="mb-3">
                <input type="text" name="name" class="form-control" required placeholder="Full Name">
            </div>
            <div class="mb-3">
                <input type="email" name="email" class="form-control" required placeholder="Email">
            </div>
            <div class="mb-3">
                <input type="password" name="password" class="form-control" required placeholder="Password">
            </div>

            <div class="mb-3">
                <label class="form-label">Select Role:</label>
                <select name="role" id="role" class="form-select" onchange="togglePharmacistFields()">
                    <option value="customer">Customer</option>
                    <option value="pharmacist">Pharmacist</option>
                </select>
            </div>

            <!-- Pharmacist-specific fields -->
            <div id="pharmacistFields" class="hidden">
                <div class="mb-3">
                    <input type="text" name="shop_name" class="form-control" placeholder="Shop Name">
                </div>
                <div class="mb-3">
                    <input type="text" name="address" class="form-control" placeholder="Address">
                </div>
                <div class="mb-3">
                    <input type="text" name="phone" class="form-control" placeholder="Phone">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100">Register</button>
        </form>
    </div>
</div>

<script>
function togglePharmacistFields() {
    var role = document.getElementById("role").value;
    var pharmacistFields = document.getElementById("pharmacistFields");
    
    if (role === "pharmacist") {
        pharmacistFields.classList.remove("hidden");
    } else {
        pharmacistFields.classList.add("hidden");
    }
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
