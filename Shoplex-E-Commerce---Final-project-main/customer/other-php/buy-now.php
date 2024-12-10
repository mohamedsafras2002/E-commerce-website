<?php

include '../php-config/db-conn.php';

session_start();

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to your cart.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity']);
$price_after_discount = doubleval($_POST['price_after_discount']);
$subtotal = doubleval($_POST['subtotal']);
$shipping_fee = doubleval($_POST['shipping_fee']);

if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}

$conn->autocommit(false);

try {
    // Start by inserting the order into the orders table
    $createOrderQuery = $conn->prepare("INSERT INTO orders (buyer_id, total_amount, total_shipping_fee) VALUES (?, ?, ?)");
    $createOrderQuery->bind_param("idd", $user_id, $subtotal, $shipping_fee); 
    if (!$createOrderQuery->execute()) {
        throw new Exception("Failed to create the order.");
    }
    $order_id = $conn->insert_id;

    // Fetch the status_id for "Pending" status
    $status_name = "Pending"; 
    $getOrderStatusQuery = $conn->prepare("SELECT status_id FROM order_status WHERE status_name = ?");
    $getOrderStatusQuery->bind_param("s", $status_name); 
    if (!$getOrderStatusQuery->execute()) {
        throw new Exception("Failed to fetch order status.");
    }

    $result = $getOrderStatusQuery->get_result(); 
    $row = $result->fetch_assoc(); 
    if (!$row) {
        throw new Exception("Invalid status name.");
    }
    $status_id = intval($row['status_id']); 

    // Insert the order item
    $addOrderItemQuery = $conn->prepare("INSERT INTO order_item (order_id, product_id, quantity, price_after_discount, subtotal, shipping_fee, status_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $addOrderItemQuery->bind_param("iiidddi", $order_id, $product_id, $quantity, $price_after_discount, $subtotal, $shipping_fee, $status_id); 
    if (!$addOrderItemQuery->execute()) {
        throw new Exception("Failed to add order item.");
    }

    // Update product stock
    $updateStockItemQuery = $conn->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ? AND stock >= ?");
    $updateStockItemQuery->bind_param("iii", $quantity, $product_id, $quantity); 
    if (!$updateStockItemQuery->execute()) {
        throw new Exception("Failed to update stock.");
    }

    // Check if stock was updated
    if ($updateStockItemQuery->affected_rows === 0) {
        throw new Exception("Insufficient stock for the requested quantity.");
    }

    // Commit the transaction
    $conn->commit();

    // Return success
    echo json_encode(['success' => true, 'message' => 'Order has been placed successfully.']);

} catch (Exception $e) {
    // Rollback transaction and return error message
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Close connections
    $createOrderQuery->close();
    $getOrderStatusQuery->close();
    $addOrderItemQuery->close();
    $updateStockItemQuery->close();
    $conn->close();
}
?>
