<?php
include('../php-config/db-conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartItemId = $_POST['cart_item_id'];

    $sql = "DELETE FROM cart_item WHERE cart_item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $cartItemId);
    $stmt->execute();

    header('Location: ../cart-page.php'); // Redirect back to the cart page
    exit;
}
?>
