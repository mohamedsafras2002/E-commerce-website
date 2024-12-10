<?php
include('../php-config/db-conn.php');
session_start();

$userId = $_SESSION['user_id'];

$cartSql = "SELECT cart_id FROM cart WHERE buyer_id = ?";
$cartStmt = $conn->prepare($cartSql);
$cartStmt->bind_param('i', $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();
$cart = $cartResult->fetch_assoc();
$cart_id = $cart['cart_id'];


$total_amount = 0;
$total_shipping_fee = 0;

$orderSql = "INSERT INTO orders (buyer_id, total_amount, total_shipping_fee) VALUES (?, ?, ?)";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param('idd', $userId, $total_amount, $total_shipping_fee); 
$orderStmt->execute();
$orderId = $conn->insert_id; 


$cartItemsSql = "SELECT * FROM cart_item WHERE cart_id = ?";
$cartItemsStmt = $conn->prepare($cartItemsSql);
$cartItemsStmt->bind_param('i', $cart_id);
$cartItemsStmt->execute();
$cartItemsResult = $cartItemsStmt->get_result();

while ($item = $cartItemsResult->fetch_assoc()) {
    $productDetailSql = "SELECT * FROM product WHERE product_id = ?";
    $productDetailStmt = $conn->prepare($productDetailSql);
    $productDetailStmt->bind_param('i', $item['product_id']);
    $productDetailStmt->execute();
    $productDetailResult = $productDetailStmt->get_result();
    $productDetail = $productDetailResult->fetch_assoc();

    $price_after_discount = floatval($productDetail['price']) - floatval($productDetail['price']) * floatval($productDetail['discount']);
    $shipping_fee = floatval($productDetail['shipping_fee']);
    $subtotal = $price_after_discount * $item['quantity'];

    $total_shipping_fee += $shipping_fee;
    $total_amount += $subtotal;

    $status_name = "Pending"; 
    $getOrderStatusQuery = $conn->prepare("SELECT status_id FROM order_status WHERE status_name = ?");
    $getOrderStatusQuery->bind_param("s", $status_name); 
    $getOrderStatusQuery->execute();
    $result = $getOrderStatusQuery->get_result();
    $row = $result->fetch_assoc();
    $status_id = intval($row['status_id']); 

    $detailSql = "INSERT INTO order_item (order_id, product_id, quantity, price_after_discount, subtotal, shipping_fee, status_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
    $detailStmt = $conn->prepare($detailSql);
    $detailStmt->bind_param('iiidddi', $orderId, $item['product_id'], $item['quantity'], $price_after_discount, $subtotal, $shipping_fee, $status_id);
    $detailStmt->execute();
}

$orderUpdateSql = "UPDATE orders SET total_amount = ?, total_shipping_fee = ? WHERE order_id = ?";
$orderUpdateStmt = $conn->prepare($orderUpdateSql);
$orderUpdateStmt->bind_param('ddi', $total_amount, $total_shipping_fee, $orderId);
$orderUpdateStmt->execute();

$clearCartSql = "DELETE FROM cart_item WHERE cart_id = ?";
$clearCartStmt = $conn->prepare($clearCartSql);
$clearCartStmt->bind_param('i', $cart_id);
$clearCartStmt->execute();


header('Location: ../index.php'); 
exit;
?>
