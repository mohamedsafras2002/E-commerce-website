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
$quantity = intval($_POST['quantity']);

// Validate product_id and quantity
if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product or quantity.']);
    exit;
}

// Disable auto-commit and begin the transaction
$conn->autocommit(false);

try {
    // Fetch the user's cart
    $cartQuery = $conn->prepare("SELECT cart_id FROM cart WHERE buyer_id = ?");
    $cartQuery->bind_param("i", $user_id);  // Bind the integer parameter
    $cartQuery->execute();
    $cartResult = $cartQuery->get_result();
    $cart = $cartResult->fetch_assoc();

    if (!$cart) {
        // Create a new cart if it doesn't exist
        $createCartQuery = $conn->prepare("INSERT INTO cart (buyer_id) VALUES (?)");
        $createCartQuery->bind_param("i", $user_id);  // Bind the integer parameter
        $createCartQuery->execute();
        $cart_id = $conn->insert_id;
    } else {
        $cart_id = $cart['cart_id'];
    }

    // Check if the product already exists in the cart
    $cartItemQuery = $conn->prepare("SELECT quantity FROM cart_item WHERE cart_id = ? AND product_id = ?");
    $cartItemQuery->bind_param("ii", $cart_id, $product_id);  // Bind the integer parameters
    $cartItemQuery->execute();
    $cartItemResult = $cartItemQuery->get_result();
    $cartItem = $cartItemResult->fetch_assoc();

    if ($cartItem) {
        // If the item is already in the cart, don't add it again
        echo json_encode(['success' => false, 'message' => 'Item already in cart.']);
    } else {
        // Add new product to the cart
        $addCartItemQuery = $conn->prepare("INSERT INTO cart_item (cart_id, product_id, quantity) VALUES (?, ?, ?)");
        $addCartItemQuery->bind_param("iii", $cart_id, $product_id, $quantity);  // Bind the integer parameters
        $addCartItemQuery->execute();
        echo json_encode(['success' => true, 'message' => 'Product added to cart successfully.']);
    }

    // Commit the transaction
    $conn->commit();

} catch (Exception $e) {
    // Roll back the transaction in case of error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
