<?php
include '../php-config/db-conn.php';

$data = json_decode(file_get_contents("php://input"), true);
$orderItemId = $data['order_item_id'];
$statusId = $data['status_id'];

if ($statusId == 3) { 
    $shippedDate = date('Y-m-d'); 
    $expectedDeliveryDate = date('Y-m-d', strtotime('+7 days')); 

    $query = "UPDATE order_item 
              SET status_id = ?, shipped_date = ?, expected_delivery_date = ?, delivered_date = NULL 
              WHERE order_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('issi', $statusId, $shippedDate, $expectedDeliveryDate, $orderItemId);
} else {
    $query = "UPDATE order_item 
              SET status_id = ? 
              WHERE order_item_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $statusId, $orderItemId);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
