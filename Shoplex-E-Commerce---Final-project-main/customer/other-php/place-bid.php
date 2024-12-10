<?php
// Include database connection
include '../php-config/db-conn.php';

// Start the session (make sure it's only called once)
session_start();

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to add items to your cart.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$auction_id = intval($_POST['auction_id']);
$bid_amount = $_POST['bid_amount']; // Raw bid amount without formatting

// Validate product_id and bid_amount
if ($product_id <= 0 || $bid_amount <= 0 || $auction_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or bid amount.']);
    exit;
}

// Disable auto-commit and begin the transaction
$conn->autocommit(false);

try {
    // Prepare the SQL query to insert the bid record
    $createBidQuery = $conn->prepare("INSERT INTO bidding_records (auction_id, product_id, bider_id, bid_amount) VALUES (?, ?, ?, ?)");
    if (!$createBidQuery) {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare query: ' . $conn->error]);
        exit;
    }

    // Bind the parameters (integer, integer, integer, float)
    $createBidQuery->bind_param("iiid", $auction_id, $product_id, $user_id, $bid_amount);

    // Execute the query
    if ($createBidQuery->execute()) {
        // If the bid was placed successfully, commit the transaction
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Bid has been placed successfully!']);
    } else {
        // If the execution failed, rollback the transaction
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => "Bid hasn't been placed successfully."]);
    }
} catch (Exception $e) {
    // Roll back the transaction in case of an error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
} finally {
    // Reset autocommit to true
    $conn->autocommit(true);
}
