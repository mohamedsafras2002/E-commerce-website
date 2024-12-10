<?php
include '../php-config/db-conn.php';
session_start();

$userId = $_SESSION['user_id']; // Assume user is logged in and their ID is stored in the session

// Fetch auctions involving the user
$query = "
    SELECT a.auction_id, a.start_time, a.end_time, a.starting_bid, a.ending_bid, 
           a.winning_bidder_id, p.product_id, p.product_name, p.description,
           (SELECT MAX(bid_amount) 
            FROM bidding_records 
            WHERE auction_id = a.auction_id) AS current_highest_bid
    FROM auction_history AS a
    JOIN product AS p ON a.product_id = p.product_id
    WHERE a.winning_bidder_id = ? OR a.auction_id IN (
        SELECT DISTINCT auction_id FROM bidding_records WHERE bider_id = ?
    )
    ORDER BY a.end_time DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $userId, $userId);
$stmt->execute();
$result = $stmt->get_result();

$auctions = [];

while ($row = $result->fetch_assoc()) {
    // Check if the user is the winner
    $isWinner = $row['winning_bidder_id'] == $userId;

    // Fetch bidding records for this auction by the user
    $biddingQuery = "SELECT bid_id, bid_amount, bid_time 
                     FROM bidding_records 
                     WHERE auction_id = ? AND bider_id = ?
                     ORDER BY bid_time DESC";
    $biddingStmt = $conn->prepare($biddingQuery);
    $biddingStmt->bind_param("ii", $row['auction_id'], $userId);
    $biddingStmt->execute();
    $biddingResult = $biddingStmt->get_result();
    $biddingRecords = $biddingResult->fetch_all(MYSQLI_ASSOC);

    $row['bids'] = $biddingRecords;
    $row['isWinner'] = $isWinner;
    $auctions[] = $row;
}

echo json_encode([
    'success' => true,
    'auctions' => $auctions,
]);
?>
