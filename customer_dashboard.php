<?php
session_start();
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] != "customer") {
    header("Location: index.php");
    exit();
}

include 'db.php';

// Get customer ID
$customer_id = $_SESSION["user_id"];

// Fetch all pharmacists
$pharmacist_sql = "SELECT id, name, shop_name FROM pharmacists";
$pharmacists = $conn->query($pharmacist_sql);

// Fetch orders with prescription, medicines, and pharmacist details
function getOrdersByStatus($conn, $customer_id, $status) {
    $sql = "SELECT o.id, p.image_path, p.extracted_text, ph.name AS pharmacist_name, ph.shop_name, o.status 
            FROM orders o 
            JOIN prescriptions p ON o.prescription_id = p.id 
            JOIN pharmacists ph ON o.pharmacist_id = ph.id
            WHERE o.customer_id = '$customer_id' AND o.status = '$status'";
    return $conn->query($sql);
}

$pendingOrders = getOrdersByStatus($conn, $customer_id, 'pending');
$completedOrders = getOrdersByStatus($conn, $customer_id, 'completed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 40px;
        }
        .btn-primary, .btn-danger {
            width: 100%;
            margin-top: 10px;
        }
        .table {
            margin-top: 20px;
        }
        .order-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center mb-4">👤 Welcome, Customer!</h2>

    <!-- Upload Prescription Section -->
    <div class="card p-4 shadow-sm">
        <h4 class="mb-3">📄 Upload Prescription</h4>
        <form action="upload_prescription.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Upload Prescription:</label>
                <input type="file" class="form-control" name="prescription" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Select Pharmacist:</label>
                <select name="pharmacist_id" class="form-select" required>
                    <?php while ($row = $pharmacists->fetch_assoc()) { ?>
                        <option value="<?= $row['id'] ?>"><?= $row['name'] ?> (<?= $row['shop_name'] ?>)</option>
                    <?php } ?>
                </select>
            </div>

            <button type="submit" class="btn btn-success">📤 Upload & Send</button>
        </form>
    </div>

    <!-- Buttons for Viewing Orders -->
    <div class="mt-4 text-center">
        <div class="d-flex justify-content-center gap-3">
            <button class="btn btn-outline-primary px-4 py-2" onclick="showOrders('pending')">📌 View Pending Orders</button>
            <button class="btn btn-outline-primary px-4 py-2" onclick="showOrders('completed')">✅ View Completed Orders</button>
        </div>
    </div>

    <!-- Orders Display -->
    <div id="ordersContainer" class="mt-4">
        <h4 id="orderTitle" class="text-center"></h4>
        <table class="table table-striped table-bordered" id="ordersTable" style="display: none;">
            <thead>
                <tr>
                    <th>Prescription</th>
                    <th>Medicines</th>
                    <th>Pharmacist</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="ordersBody"></tbody>
        </table>
    </div>

    <a href="logout.php" class="btn btn-danger mt-4">🚪 Logout</a>
</div>

<script>
    let pendingOrders = <?= json_encode($pendingOrders->fetch_all(MYSQLI_ASSOC)); ?>;
    let completedOrders = <?= json_encode($completedOrders->fetch_all(MYSQLI_ASSOC)); ?>;

    function showOrders(status) {
        let orders = status === "pending" ? pendingOrders : completedOrders;
        let title = status === "pending" ? "📌 Pending Orders" : "✅ Completed Orders";
        
        let ordersBody = document.getElementById("ordersBody");
        let ordersTable = document.getElementById("ordersTable");
        let orderTitle = document.getElementById("orderTitle");

        orderTitle.innerText = title;
        ordersBody.innerHTML = "";

        if (orders.length > 0) {
            orders.forEach(order => {
                let medicines = order.extracted_text ? order.extracted_text.replace(/\n/g, "<br>") : "No medicines extracted";
                let row = `<tr>
                    <td><img src="prescriptions/${order.image_path}" alt="Prescription" class="order-image" style="width:80px; height:80px;" onerror="this.onerror=null;this.src='default-prescription.png';"></td>
                    <td>${medicines}</td>
                    <td>${order.pharmacist_name} (${order.shop_name})</td>
                    <td>${order.status}</td>
                </tr>`;
                ordersBody.innerHTML += row;
            });
            ordersTable.style.display = "table";
        } else {
            ordersTable.style.display = "none";
            ordersBody.innerHTML = `<tr><td colspan="4" class="text-center">No ${status} orders found.</td></tr>`;
        }
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
