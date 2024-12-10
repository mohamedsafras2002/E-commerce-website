<?php
include('../php-config/db-conn.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$userId = $_SESSION['user_id'];

// Validate auction_id and product_id are passed
if (!isset($_GET['auction_id']) || !isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing auction_id or product_id.']);
    exit;
}

$auctionId = intval($_GET['auction_id']);
$productId = intval($_GET['product_id']);

// Check if the product from the specific auction is already purchased
$purchaseCheckSql = "SELECT 1 FROM order_item WHERE auction_id = ? AND product_id = ? AND order_id IN (
    SELECT order_id FROM orders WHERE buyer_id = ?
)";
$purchaseCheckStmt = $conn->prepare($purchaseCheckSql);
$purchaseCheckStmt->bind_param('iii', $auctionId, $productId, $userId);
$purchaseCheckStmt->execute();
$purchaseCheckResult = $purchaseCheckStmt->get_result();

if ($purchaseCheckResult->num_rows > 0) {
    echo json_encode(['success' => true, 'purchased' => true]); // Product purchased
} else {
    echo json_encode(['success' => true, 'purchased' => false]); // Product not purchased
}

exit;
?>
