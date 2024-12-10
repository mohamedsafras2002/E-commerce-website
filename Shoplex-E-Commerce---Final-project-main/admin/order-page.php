<?php
include 'php-config/db-conn.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    $userId = $_SESSION['admin_id'];
    $adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; 

    $sql = "SELECT email FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminEmail = $result->fetch_assoc()['email'];
} else {
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/order-page.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-content">
                <span>Admin Panel</span> 
                <span style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($adminName); ?></span>
                <span style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($adminEmail); ?></span>
            </div>
            <div class="hamburger" onclick="toggleSidebar()">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard-page.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="user-page.php"><i class="fas fa-users"></i> <span>Users</span></a>
            <a href="inventory-page.php"><i class="fas fa-archive"></i> <span>Inventories</span></a>
            <a href="order-page.php" class="active"><i class="fas fa-box"></i> <span>Orders</span></a>
            <a href="bidding-record-page.php"><i class="fas fa-gavel"></i> <span>Bidding Records</span></a>
            <a href="sales-analysis.php"><i class="fas fa-chart-line"></i> <span>Sales Analysis</span></a>
            <a href="message-page.php"><i class="fas fa-inbox"></i> <span>Messages</span></a>
            <a href="banner-page.php"><i class="fas fa-ad"></i> <span>Banners</span></a>
            <a href="php-config/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Order Management</h1>
        </div>

        <table id="ordersTable" class="inventory-table">
            <thead>
                <tr>
                    <th>Order Item ID</th>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Buyer</th>
                    <th>Quantity</th>
                    <th>Status</th>
                    <th>Shipped Date</th>
                    <th>Expected Delivery</th>
                    <th>Delivered Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "
                    SELECT 
                        oi.order_item_id, 
                        o.order_id, 
                        p.product_name, 
                        u.name AS buyer_name, 
                        oi.quantity, 
                        s.status_name, 
                        oi.shipped_date, 
                        oi.expected_delivery_date, 
                        oi.delivered_date, 
                        oi.status_id
                    FROM 
                        order_item oi
                    JOIN 
                        orders o ON oi.order_id = o.order_id
                    JOIN 
                        user u ON o.buyer_id = u.user_id
                    JOIN 
                        product p ON oi.product_id = p.product_id
                    JOIN 
                        order_status s ON oi.status_id = s.status_id";
                
                $result = mysqli_query($conn, $query);

                $statusQuery = "SELECT status_id, status_name FROM order_status";
                $statusResult = mysqli_query($conn, $statusQuery);
                $statuses = [];
                while ($row = mysqli_fetch_assoc($statusResult)) {
                    $statuses[] = $row;
                }

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $orderItemId = $row['order_item_id'];
                        $orderId = $row['order_id'];
                        $productName = $row['product_name'];
                        $buyerName = $row['buyer_name'];
                        $quantity = $row['quantity'];
                        $statusName = $row['status_name'];
                        $shippedDate = $row['shipped_date'] ?? "Not Shipped";
                        $expectedDeliveryDate = $row['expected_delivery_date'] ?? "N/A";
                        $deliveredDate = $row['delivered_date'] ?? "Not Delivered";

                        $isDisabled = ($statusName === "Delivered" || $statusName === "Shipped") ? "disabled" : "";
                
                        echo "<tr>
                            <td>$orderItemId</td>
                            <td>$orderId</td>
                            <td>$productName</td>
                            <td>$buyerName</td>
                            <td>$quantity</td>
                            <td>
                                <select class='status-dropdown' data-order-item-id='$orderItemId' $isDisabled>";
                        foreach ($statuses as $status) {
                            if ($status['status_name'] === "Delivered" && $statusName !== "Delivered") {
                                continue; 
                            }
                            $selected = $status['status_name'] === $statusName ? "selected" : "";
                            echo "<option value='{$status['status_id']}' $selected>{$status['status_name']}</option>";
                        }
                        echo "</select>
                            </td>
                            <td>$shippedDate</td>
                            <td>$expectedDeliveryDate</td>
                            <td>$deliveredDate</td>
                            <td>
                                <button class='update-btn' data-order-item-id='$orderItemId' $isDisabled>Update</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No orders found.</td></tr>";
                }
                
                
                
                ?>
            </tbody>
        </table>
    </div>

    <script src="javascript/order-page.js"></script>
    <script src="javascript/side-navbar.js"></script>
        
</body>

</html>
