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

// Check if this auction product has already been checked out
$orderCheckSql = "SELECT * FROM order_item WHERE product_id = ? AND auction_id = ? AND order_id IN (
    SELECT order_id FROM orders WHERE buyer_id = ?
)";
$orderCheckStmt = $conn->prepare($orderCheckSql);
$orderCheckStmt->bind_param('iii', $productId, $auctionId, $userId);
$orderCheckStmt->execute();
$orderCheckResult = $orderCheckStmt->get_result();

if ($orderCheckResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'This auction product has already been checked out.']);
    exit;
}

// Fetch the auction details to confirm the user won the auction
$auctionSql = "SELECT * FROM auction_history WHERE auction_id = ? AND product_id = ? AND winning_bidder_id = ?";
$auctionStmt = $conn->prepare($auctionSql);
$auctionStmt->bind_param('iii', $auctionId, $productId, $userId);
$auctionStmt->execute();
$auctionResult = $auctionStmt->get_result();
$auction = $auctionResult->fetch_assoc();

if (!$auction) {
    echo json_encode(['success' => false, 'message' => 'You did not win this auction.']);
    exit;
}

// Fetch product details
$productSql = "SELECT * FROM product WHERE product_id = ?";
$productStmt = $conn->prepare($productSql);
$productStmt->bind_param('i', $productId);
$productStmt->execute();
$productResult = $productStmt->get_result();
$product = $productResult->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

// Check if product stock is available
if ($product['stock'] < 1) {
    echo json_encode(['success' => false, 'message' => 'Product is out of stock.']);
    exit;
}

// Initialize variables
$priceAfterDiscount = floatval($auction['ending_bid']);
$shippingFee = floatval($product['shipping_fee']);
$totalAmount = $priceAfterDiscount;

// Fetch "Pending" status ID
$statusName = "Pending";
$getOrderStatusQuery = $conn->prepare("SELECT status_id FROM order_status WHERE status_name = ?");
$getOrderStatusQuery->bind_param("s", $statusName);
$getOrderStatusQuery->execute();
$statusResult = $getOrderStatusQuery->get_result();
$statusRow = $statusResult->fetch_assoc();
$statusId = intval($statusRow['status_id']); // Get status ID

// Create a new order
$orderSql = "INSERT INTO orders (buyer_id, total_amount, total_shipping_fee) VALUES (?, ?, ?)";
$orderStmt = $conn->prepare($orderSql);
$orderStmt->bind_param('idd', $userId, $totalAmount, $shippingFee);
$orderStmt->execute();
$orderId = $conn->insert_id; // Get the generated order_id

// Insert the auction-won product into order_item
$orderItemSql = "INSERT INTO order_item (order_id, product_id, auction_id, quantity, price_after_discount, subtotal, shipping_fee, status_id) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$orderItemStmt = $conn->prepare($orderItemSql);
$quantity = 1; // Auction items are typically single quantity
$subtotal = $priceAfterDiscount; // Subtotal is the bid price
$orderItemStmt->bind_param('iiidddii', $orderId, $productId, $auctionId, $quantity, $priceAfterDiscount, $subtotal, $shippingFee, $statusId);
$orderItemStmt->execute();

// Decrease product stock by 1
$updateStockSql = "UPDATE product SET stock = stock - 1 WHERE product_id = ? AND stock > 0";
$updateStockStmt = $conn->prepare($updateStockSql);
$updateStockStmt->bind_param('i', $productId);
if (!$updateStockStmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Failed to update product stock: ' . $conn->error]);
    exit;
}

echo json_encode(['success' => true, 'message' => 'Checkout successful!']);
exit;
?>
