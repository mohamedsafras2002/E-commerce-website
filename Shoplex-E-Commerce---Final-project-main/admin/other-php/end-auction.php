<?php
include '../php-config/db-conn.php';

if (isset($_GET['auction_id']) && isset($_GET['product_id'])) {
    $auctionId = intval($_GET['auction_id']);
    $productId = intval($_GET['product_id']);
    $currentDate = date('Y-m-d H:i:s');

    $auctionQuery = "SELECT end_time FROM auction_history WHERE auction_id = ?";
    $auctionStmt = $conn->prepare($auctionQuery);
    $auctionStmt->bind_param("i", $auctionId);
    $auctionStmt->execute();
    $auctionResult = $auctionStmt->get_result();
    $auction = $auctionResult->fetch_assoc();

    if ($auction['end_time'] === NULL) {
        echo json_encode(['success' => false, 'message' => 'Auction has not started yet. Cannot end the auction.']);
        exit;
    }

    $query = "SELECT bider_id, bid_amount 
              FROM bidding_records 
              WHERE auction_id = ? 
              ORDER BY bid_amount DESC, bid_time ASC 
              LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $auctionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $winnerBidderId = $row['bider_id'] ?? null;
    $highestBid = $row['bid_amount'] ?? null;

    if ($highestBid !== null) {
       
        $updateQuery = "UPDATE auction_history 
                        SET end_time = ?, ending_bid = ?, winning_bidder_id = ?, is_end = 1
                        WHERE auction_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("sdii", $currentDate, $highestBid, $winnerBidderId, $auctionId);
        if ($updateStmt->execute()) {
      
            $productQuery = "SELECT bid_starting_price FROM product WHERE product_id = ?";
            $productStmt = $conn->prepare($productQuery);
            $productStmt->bind_param("i", $productId);
            $productStmt->execute();
            $productResult = $productStmt->get_result();
            $productRow = $productResult->fetch_assoc();
            $startingBid = $productRow['bid_starting_price']; 

            
            $newAuctionQuery = "INSERT INTO auction_history (start_time, end_time, product_id, starting_bid) 
                                VALUES (?, null, ?, ?)";
            $newAuctionStmt = $conn->prepare($newAuctionQuery);
            $newAuctionStmt->bind_param("sdi", $currentDate, $productId, $startingBid);
            if ($newAuctionStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Auction ended successfully. New auction started.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error creating new auction']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating auction']);
        }
    } else {

        $updateQuery = "UPDATE auction_history 
                        SET end_time = ?, ending_bid = NULL, winning_bidder_id = NULL, is_end = 1
                        WHERE auction_id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $currentDate, $auctionId);
        if ($updateStmt->execute()) {

            $productQuery = "SELECT bid_starting_price FROM product WHERE product_id = ?";
            $productStmt = $conn->prepare($productQuery);
            $productStmt->bind_param("i", $productId);
            $productStmt->execute();
            $productResult = $productStmt->get_result();
            $productRow = $productResult->fetch_assoc();
            $startingBid = $productRow['bid_starting_price']; 

            $newAuctionQuery = "INSERT INTO auction_history (start_time, product_id, starting_bid) 
                                VALUES (?, ?, ?)";
            $newAuctionStmt = $conn->prepare($newAuctionQuery);
            $newAuctionStmt->bind_param("sdi", $currentDate, $productId, $startingBid);
            if ($newAuctionStmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Auction ended with no bids. New auction started.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error closing auction with no bids.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error updating auction']);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid parameters.']);
}
?>
