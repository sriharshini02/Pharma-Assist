<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "pharmacist") {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Get pharmacist ID
$user_id = $_SESSION["user_id"];
$pharmacist_sql = "SELECT id FROM pharmacists WHERE name = (SELECT name FROM users WHERE id = '$user_id')";
$pharmacist_result = $conn->query($pharmacist_sql);

if ($pharmacist_result->num_rows > 0) {
    $pharmacist_row = $pharmacist_result->fetch_assoc();
    $pharmacist_id = $pharmacist_row["id"];

    // Fetch orders by status
    function getOrdersByStatus($conn, $pharmacist_id, $status) {
        $sql = "SELECT o.customer_id, u.name AS customer_name, m.medicine_name, m.dosage 
                FROM orders o 
                JOIN users u ON o.customer_id = u.id 
                JOIN prescriptions p ON o.prescription_id = p.id 
                JOIN medicines m ON p.id = m.prescription_id
                WHERE o.pharmacist_id = '$pharmacist_id' AND o.status = '$status'";
        return $conn->query($sql);
    }

    $pendingOrders = getOrdersByStatus($conn, $pharmacist_id, 'pending');
    $completedOrders = getOrdersByStatus($conn, $pharmacist_id, 'completed');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pharmacist Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 40px;
        }
        .table {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">👨‍⚕️ Welcome, Pharmacist!</h2>

    <!-- Tabs for Pending & Completed Orders -->
    <ul class="nav nav-tabs" id="orderTabs">
        <li class="nav-item">
            <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pendingOrders">Pending Orders</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="completed-tab" data-bs-toggle="tab" data-bs-target="#completedOrders">Completed Orders</button>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Pending Orders -->
        <div class="tab-pane fade show active" id="pendingOrders">
            <?php displayOrders($pendingOrders, "No pending orders."); ?>
        </div>

        <!-- Completed Orders -->
        <div class="tab-pane fade" id="completedOrders">
            <?php displayOrders($completedOrders, "No completed orders."); ?>
        </div>
    </div>

    <a href="logout.php" class="btn btn-danger mt-4">Logout</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Function to display grouped orders
function displayOrders($orders, $emptyMessage) {
    if ($orders->num_rows > 0) {
        $customers = [];
        while ($row = $orders->fetch_assoc()) {
            $customers[$row["customer_name"]][] = [
                "medicine_name" => $row["medicine_name"],
                "dosage" => $row["dosage"]
            ];
        }

        echo '<table class="table table-striped table-bordered">';
        echo '<thead><tr><th>Customer</th><th>Medicines</th></tr></thead>';
        echo '<tbody>';

        foreach ($customers as $customer_name => $medicines) {
            echo "<tr>";
            echo "<td><strong>$customer_name</strong></td>";
            echo "<td>";
            foreach ($medicines as $medicine) {
                echo "{$medicine['medicine_name']} ({$medicine['dosage']})<br>";
            }
            echo "</td>";
            echo "</tr>";
        }

        echo '</tbody></table>';
    } else {
        echo "<p class='text-center mt-3'>$emptyMessage</p>";
    }
}
?>
