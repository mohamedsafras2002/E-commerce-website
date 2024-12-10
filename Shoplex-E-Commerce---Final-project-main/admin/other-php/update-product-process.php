<?php
include '../php-config/db-conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $productId = $_POST['productId'];
    $productType = $_POST['productType'];
    $productName = $_POST['productName'];
    $description = $_POST['description'];
    $parentCategoryId = intval($_POST['parentCategory']);
    $subCategoryId = intval($_POST['subCategory']);

    $price = isset($_POST['price']) ? number_format(floatval($_POST['price']), 2, '.', '') : null;
    $discount = number_format(floatval($_POST['discount']) / 100, 2, '.', '');
    $stock = intval($_POST['stock']);
    $shippingFee = number_format(floatval($_POST['shippingFee']), 2, '.', '');

    $bidStartingPrice = isset($_POST['bidStartingPrice']) ? number_format(floatval($_POST['bidStartingPrice']), 2, '.', '') : null;
    $bidStartDate = $_POST['bidStartDate'] ?? null;
    $bidEndDate = $_POST['bidEndDate'] ?? null;

    $uploadedImages = [];
    $uploadDir = "../../images/product-images/";

    if (!file_exists($uploadDir)) {
        if (!mkdir($uploadDir, 0777, true)) {
            die("Failed to create upload directory: $uploadDir");
        }
    }

    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
            $originalName = basename($_FILES['images']['name'][$key]);
            $sanitizedFilename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $originalName);
            $uniqueFilename = uniqid() . "_" . $sanitizedFilename;
            $targetPath = $uploadDir . $uniqueFilename;

            if (is_uploaded_file($tmpName)) {
                if (move_uploaded_file($tmpName, $targetPath)) {
                    $uploadedImages[] = "/images/product-images/" . $uniqueFilename;
                }
            }
        }
    }

    try {
        $conn->begin_transaction();

        if ($productType == "normal") {
            $updateProductQuery = "
                UPDATE product SET 
                    category_id = ?, 
                    product_name = ?, 
                    description = ?, 
                    price = ?, 
                    stock = ?, 
                    discount = ?, 
                    shipping_fee = ? 
                WHERE product_id = ?";
            
            $stmt = $conn->prepare($updateProductQuery);
            $stmt->bind_param(
                "issdiddi",
                $subCategoryId,
                $productName,
                $description,
                $price,
                $stock,
                $discount,
                $shippingFee,
                $productId
            );
            $stmt->execute();

            if (!empty($uploadedImages)) {
                $deleteExistingImagesQuery = "DELETE FROM product_picture WHERE product_id = ?";
                $deleteStmt = $conn->prepare($deleteExistingImagesQuery);
                $deleteStmt->bind_param("i", $productId);
                $deleteStmt->execute();
                $deleteStmt->close();

                $addProductImageQuery = "INSERT INTO product_picture (product_id, picture_path, default_picture) VALUES (?, ?, ?)";
                $imageStmt = $conn->prepare($addProductImageQuery);

                foreach ($uploadedImages as $index => $imagePath) {
                    $defaultPicture = ($index === 0) ? 1 : 0;
                    $imageStmt->bind_param("isi", $productId, $imagePath, $defaultPicture);
                    $imageStmt->execute();
                }
                $imageStmt->close();
            }
        }

        if ($productType == "bidding") {
            $updateBiddingProductQuery = "
                UPDATE product SET 
                    category_id = ?, 
                    product_name = ?, 
                    description = ?, 
                    bid_starting_price = ?, 
                    stock = ?, 
                    shipping_fee = ?, 
                    bid_activate = ? 
                WHERE product_id = ?";
            
            $stmt = $conn->prepare($updateBiddingProductQuery);
            $bid_activate = 1;
            $stmt->bind_param(
                "issdidii",
                $subCategoryId,
                $productName,
                $description,
                $bidStartingPrice,
                $stock,
                $shippingFee,
                $bid_activate,
                $productId
            );
            $stmt->execute();

            $updateAuctionHistoryQuery = "
                UPDATE auction_history 
                SET starting_bid = ?, start_time = ?, end_time = ? 
                WHERE product_id = ? AND is_end = 0";
            
            $stmt = $conn->prepare($updateAuctionHistoryQuery);
            $stmt->bind_param("dssi", $bidStartingPrice, $bidStartDate, $bidEndDate, $productId);
            $stmt->execute();

            if (!empty($uploadedImages)) {
                $deleteExistingImagesQuery = "DELETE FROM product_picture WHERE product_id = ?";
                $deleteStmt = $conn->prepare($deleteExistingImagesQuery);
                $deleteStmt->bind_param("i", $productId);
                $deleteStmt->execute();
                $deleteStmt->close();

                $addProductImageQuery = "INSERT INTO product_picture (product_id, picture_path, default_picture) VALUES (?, ?, ?)";
                $imageStmt = $conn->prepare($addProductImageQuery);

                foreach ($uploadedImages as $index => $imagePath) {
                    $defaultPicture = ($index === 0) ? 1 : 0;
                    $imageStmt->bind_param("isi", $productId, $imagePath, $defaultPicture);
                    $imageStmt->execute();
                }
                $imageStmt->close();
            }
        }


        $conn->commit();

        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
       
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $stmt->close();
    $conn->close();
}
?>
