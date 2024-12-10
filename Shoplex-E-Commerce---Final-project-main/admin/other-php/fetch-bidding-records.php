<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../php-config/db-conn.php';
header('Content-Type: application/json');

if (isset($_GET['auction_id'])) {
    $auctionId = intval($_GET['auction_id']); 

    $query = "SELECT bid_id, bider_id, u.name, bid_amount, bid_time 
              FROM bidding_records 
              JOIN user u
              ON u.user_id = bidding_records.bider_id
              WHERE auction_id = ?";
    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $auctionId);
        $stmt->execute();
        $result = $stmt->get_result();

        $biddingRecords = [];
        while ($row = $result->fetch_assoc()) {
            $biddingRecords[] = $row;
        }

        echo json_encode($biddingRecords);
    } else {
        echo json_encode(["error" => "Failed to prepare query."]);
    }
} else {
    echo json_encode(["error" => "auction_id parameter is required."]);
}

$conn->close();
