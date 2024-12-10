<?php
include '../php-config/db-conn.php'; // Database connection

header('Content-Type: application/json'); // Return response as JSON

if (isset($_GET['parent_id'])) {
    // Fetch subcategories
    $parentId = $_GET['parent_id'];
    $query = "SELECT category_id, category_name FROM category WHERE parent_category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $parentId);
    $stmt->execute();
    $result = $stmt->get_result();

    $subCategories = [];
    while ($row = $result->fetch_assoc()) {
        $subCategories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name']
        ];
    }

    echo json_encode($subCategories);
} else {
    // Fetch parent categories
    $query = "SELECT category_id, category_name FROM category WHERE parent_category_id IS NULL";
    $result = $conn->query($query);

    $parentCategories = [];
    while ($row = $result->fetch_assoc()) {
        $parentCategories[] = [
            'category_id' => $row['category_id'],
            'category_name' => $row['category_name']
        ];
    }

    echo json_encode($parentCategories);
}
?>
