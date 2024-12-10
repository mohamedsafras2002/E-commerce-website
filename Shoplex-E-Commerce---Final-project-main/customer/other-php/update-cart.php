<?php
include('../php-config/db-conn.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cartItemId = $_POST['cart_item_id'];
    $quantity = $_POST['quantity'];

    $sql = "UPDATE cart_item SET quantity = ? WHERE cart_item_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $quantity, $cartItemId);
    $stmt->execute();

    header('Location: ../cart-page.php'); // Redirect back to the cart page
    exit;
}
?>
