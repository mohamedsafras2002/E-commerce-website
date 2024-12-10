<?php
include '../php-config/db-conn.php';

if (isset($_GET['product_id'])) {
    $productId = (int)$_GET['product_id'];

    // Validate product_id
    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid product ID.']);
        exit;
    }

    // Fetch product details
    $productQuery = $conn->prepare("SELECT product_id, product_name, description, price, discount, stock, shipping_fee, bid_activate, bid_starting_price FROM product WHERE product_id = ?");
    $productQuery->bind_param("i", $productId);
    $productQuery->execute();
    $productResult = $productQuery->get_result();

    if ($productResult->num_rows > 0) {
        $product = $productResult->fetch_assoc();

        // Fetch all product images
        $imagesQuery = $conn->prepare("SELECT picture_path FROM product_picture WHERE product_id = ?");
        $imagesQuery->bind_param("i", $productId);
        $imagesQuery->execute();
        $imagesResult = $imagesQuery->get_result();

        $pictures = [];
        while ($image = $imagesResult->fetch_assoc()) {
            $pictures[] = $image['picture_path'];
        }

        // Calculate discounted price
        $price = (float)$product['price'];
        $discount = (float)$product['discount'];
        $discountedPrice = $discount > 0 ? $price - ($price * $discount) : $price;
        $stock = (int)$product['stock'];
        $shippingFee = (float)$product['shipping_fee'];

        // Fetch reviews and calculate product rating
        $reviewQuery = "
            SELECT 
                pr.*, 
                u.name AS reviewer_name 
            FROM 
                product_review pr
            JOIN 
                user u 
            ON 
                pr.buyer_id = u.user_id
            WHERE 
                pr.product_id = ?
        ";
        $reviewQuery = $conn->prepare($reviewQuery);
        $reviewQuery->bind_param("i", $productId);
        $reviewQuery->execute();
        $reviewResult = $reviewQuery->get_result();

        $totalRating = 0;
        $count = 0;
        $productRating = 0;

        $reviewDetails = [];
        if ($reviewResult && $reviewResult->num_rows > 0) {
            while ($reviewRow = $reviewResult->fetch_assoc()) {
                $totalRating += (int)$reviewRow["rating"];
                $count++;
                $reviewDetails[] = $reviewRow; // Append each review to the array
            }

            // Calculate average rating
            if ($count > 0) {
                $productRating = floatval($totalRating / $count);
            }
        }

        // Prepare response data
        $response = [
            'success' => true,
            'productId' => $product['product_id'],
            'product_name' => $product['product_name'],
            'description' => $product['description'],
            'product_rating' => $productRating,
            'product_review_count' => $count,
            'original_price' => number_format($price, 2),
            'discounted_price' => number_format($discountedPrice, 2),
            'discount_percentage' => $discount > 0 ? $discount * 100 : 0,
            'pictures' => $pictures,
            'stock' => $stock,
            'shipping_fee' => $shippingFee,
            'bid_activate' => (int)$product['bid_activate'],
            'bid_starting_price' => (float)$product['bid_starting_price'],
            'review_details' => $reviewDetails // Add review details to the response
        ];

        // If the product has auction details, fetch auction history and bidding records
        if ($product["bid_activate"] == 1) {
            $auctionHistoryQuery = $conn->prepare("SELECT * FROM auction_history WHERE product_id = ? AND end_time > ? AND is_end = 0");
            $currentDt = date('Y-m-d H:i:s');
            $auctionHistoryQuery->bind_param("is", $productId, $currentDt);
            $auctionHistoryQuery->execute();
            $auctionHistoryResult = $auctionHistoryQuery->get_result();

            if ($auctionHistoryResult->num_rows > 0) {
                $auctionHistory = $auctionHistoryResult->fetch_assoc();

                $biddingRecordsQuery = $conn->prepare("SELECT * FROM bidding_records WHERE product_id = ? AND auction_id = ?");
                $biddingRecordsQuery->bind_param("ii", $productId, $auctionHistory["auction_id"]);
                $biddingRecordsQuery->execute();
                $biddingRecordsResults = $biddingRecordsQuery->get_result();

                // Fetch all bidding records
                $biddingRecords = [];
                while ($row = $biddingRecordsResults->fetch_assoc()) {
                    $biddingRecords[] = $row;
                }

                // Add auction history and bidding records to the response
                $response['auction_history'] = $auctionHistory;
                $response['bidding_records'] = $biddingRecords;
            } else {
                $response['auction_history'] = [];
                $response['bidding_records'] = [];
            }
        }
    } else {
        // If product not found
        $response = ['success' => false, 'message' => 'Product not found.'];
    }

    // Return JSON response
    echo json_encode($response);
} else {
    // If no product_id provided
    echo json_encode(['success' => false, 'message' => 'No product ID provided.']);
}
