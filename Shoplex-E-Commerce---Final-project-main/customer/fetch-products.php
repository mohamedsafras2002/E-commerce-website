<?php
include('php-config/db-conn.php');

// Get the POST data
$data = json_decode(file_get_contents("php://input"), true);
$response = ['success' => false, 'products' => []];

if (isset($data['category_id'])) {
    $category_id = intval($data['category_id']);

    // Query to fetch products by category
    $query = "SELECT product_id, name, description, price FROM products WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch products and add them to the response
        while ($row = $result->fetch_assoc()) {
            $response['products'][] = [
                'id' => $row['product_id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => $row['price']
            ];
        }
        $response['success'] = true;
    } else {
        $response['message'] = 'No products found for this category.';
    }
} else {
    $response['message'] = 'Invalid category ID.';
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
