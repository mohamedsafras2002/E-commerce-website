<?php
include('../php-config/db-conn.php');
header('Content-Type: application/json');
session_start();  // Ensure session is started before accessing session variables

// Read JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Validate the data
if (!isset($data['order_item_id']) || !isset($data['rating']) || !isset($data['review'])) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;  // Ensure user_id exists in session
$orderItemId = $data['order_item_id'];
$rating = (int)$data['rating'];  // Cast rating to integer for validation
$review = $data['review'];

if (!$user_id) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

if ($orderItemId && $rating && $review) {
    // Prepare query to get the product_id from the order_item table
    $getProductIdQuery = $conn->prepare("SELECT product_id FROM order_item WHERE order_item_id = ?");
    $getProductIdQuery->bind_param('i', $orderItemId);
    
    if (!$getProductIdQuery->execute()) {
        echo json_encode(['success' => false, 'error' => 'Failed to retrieve product ID']);
        exit;
    }
    
    $productIdResult = $getProductIdQuery->get_result();
    if ($productIdResult->num_rows == 0) {
        echo json_encode(['success' => false, 'error' => 'Order item not found']);
        exit;
    }

    $product = $productIdResult->fetch_assoc();
    $productId = $product['product_id'];

    // Insert the review into the product_review table
    $stmt = $conn->prepare("INSERT INTO product_review (product_id, buyer_id, rating, review_content) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('iiis', $productId, $user_id, $rating, $review);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $conn->error]);
    }
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
}

$conn->close();
?>
