<?php
include '../php-config/db-conn.php';

$type = $_GET['type'] ?? 'normal';

if ($type === 'normal') {
    $query = "
        SELECT 
            p.product_id, 
            p.product_name, 
            p.description, 
            c.category_name AS category_name, 
            p.price, 
            p.discount, 
            bid_activate,
            p.stock, 
            p.shipping_fee, 
            p.created_at, 
            p.updated_at,
            pp.picture_path AS picture_path
        FROM 
            product p
        LEFT JOIN 
            category c ON p.category_id = c.category_id
        LEFT JOIN 
            product_picture pp ON p.product_id = pp.product_id
        WHERE 
            p.bid_activate = 0";
} else if ($type === 'bidding') {
    $query = "
        SELECT 
            p.product_id, 
            p.product_name, 
            p.description, 
            c.category_name AS category_name, 
            pc.category_name AS parent_category_name,
            p.stock, 
            p.bid_starting_price,
            p.bid_activate, 
            p.shipping_fee, 
            p.created_at, 
            p.updated_at,
            ah.winning_bidder_id,
            ah.start_time,  
            ah.end_time,
            ah.starting_bid,
            ah.ending_bid,
            pp.picture_path AS picture_path,
            ah.auction_id,
            ah.product_id AS auction_product_id
        FROM 
            product p
        LEFT JOIN 
            category c ON p.category_id = c.category_id 
        LEFT JOIN 
            category pc ON c.parent_category_id = pc.category_id 
        LEFT JOIN 
            auction_history ah ON p.product_id = ah.product_id  
        LEFT JOIN 
            product_picture pp ON p.product_id = pp.product_id
        WHERE 
            p.bid_activate = 1";
} else {
    echo json_encode(['error' => 'Invalid product type']);
    exit;
}

$result = $conn->query($query);
$products = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Initialize or fetch existing product data
        if (!isset($products[$row['product_id']])) {
            $products[$row['product_id']] = [
                'product_id' => $row['product_id'],
                'product_name' => $row['product_name'],
                'description' => $row['description'],
                'category_name' => $row['category_name'],
                'parent_category_name' => $row['parent_category_name'] ?? null, 
                'price' => $row['price'] ?? null,
                'discount' => $row['discount'] ?? null,
                'bid_starting_price' => $row['bid_starting_price'] ?? null, 
                'bid_activate' => $row['bid_activate'],
                'winning_bidder_id' => $row['winning_bidder_id'] ?? null, 
                'stock' => $row['stock'],
                'shipping_fee' => $row['shipping_fee'],
                'start_time' => $row['start_time'] ?? null, 
                'end_time' => $row['end_time'] ?? null, 
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at'],
                'product_images' => [],
                'auction_history' => [] // Add auction_history array
            ];
        }

        // Add product images
        $products[$row['product_id']]['product_images'][] = $row['picture_path'];

        // Add auction history if this product has auction data
        if (isset($row['auction_id'])) {
            $auctionData = [
                'auction_id' => $row['auction_id'],
                'start_time' => $row['start_time'],
                'end_time' => $row['end_time'],
                'starting_bid' => $row['starting_bid'], // Include starting_bid
                'ending_bid' => $row['ending_bid'], // Include ending_bid
                'winning_bidder_id' => $row['winning_bidder_id']
            ];
            $products[$row['product_id']]['auction_history'][] = $auctionData;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(array_values($products)); // Return the data with indexed array format
?>
