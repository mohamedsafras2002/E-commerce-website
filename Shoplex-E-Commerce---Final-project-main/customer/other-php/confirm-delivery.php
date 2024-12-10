<?php
include('../php-config/db-conn.php');

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['order_item_id'])) {
    $orderItemId = $data['order_item_id'];

    // Update the order status to 'Delivered' in the database
    $sql = "UPDATE order_item SET status_id = (SELECT status_id FROM order_status WHERE status_name = 'Delivered'), delivered_date = NOW() WHERE order_item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $orderItemId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request data']);
}
$conn->close();
?>
