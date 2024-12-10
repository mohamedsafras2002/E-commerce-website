<?php
include '../php-config/db-conn.php'; 

$productId = $_GET['productId'] ?? null;

if (!$productId) {
    echo json_encode(['error' => 'Product ID is required.']);
    exit;
}

$parentCategoriesQuery = "SELECT category_id, category_name FROM category WHERE parent_category_id IS NULL";
$parentCategoriesResult = $conn->query($parentCategoriesQuery);

$parentCategories = [];
while ($row = $parentCategoriesResult->fetch_assoc()) {
    $parentCategories[] = $row;
}

$subCategoriesQuery = "SELECT category_id, category_name, parent_category_id FROM category WHERE parent_category_id IS NOT NULL";
$subCategoriesResult = $conn->query($subCategoriesQuery);

$subCategories = [];
while ($row = $subCategoriesResult->fetch_assoc()) {
    $subCategories[$row['parent_category_id']][] = $row; 
}

$productQuery = "
    SELECT 
        c1.category_name AS subCategoryName, 
        c2.category_name AS parentCategoryName,
        c1.category_id AS subCategoryId,
        c2.category_id AS parentCategoryId
    FROM product p
    LEFT JOIN category c1 ON p.category_id = c1.category_id
    LEFT JOIN category c2 ON c1.parent_category_id = c2.category_id
    WHERE p.product_id = ?
";

$stmt = $conn->prepare($productQuery);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$productData = $result->fetch_assoc();

echo json_encode([
    'parentCategories' => $parentCategories,
    'subCategories' => isset($subCategories[$productData['parentCategoryId']]) ? $subCategories[$productData['parentCategoryId']] : [],
    'parentCategoryName' => $productData['parentCategoryName'],
    'subCategoryName' => $productData['subCategoryName'],
    'parentCategoryId' => $productData['parentCategoryId'],
    'subCategoryId' => $productData['subCategoryId'],
]);
?>
