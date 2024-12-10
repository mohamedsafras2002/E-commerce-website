<?php
include 'php-config/db-conn.php';
session_start();

if (isset($_SESSION['admin_id'])) {
    $userId = $_SESSION['admin_id'];
    $adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin'; 

    $sql = "SELECT email FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $adminEmail = $result->fetch_assoc()['email'];
} else {
    header('Location: index.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bidding Records</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <!-- <link rel="stylesheet" href="css/inventory-page.css"> -->
    <link rel="stylesheet" href="css/bidding-records.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-header-content">
                <span>Admin Panel</span> 
                <span style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($adminName); ?></span>
                <span style="font-weight: bold; color: #fff;"><?php echo htmlspecialchars($adminEmail); ?></span>
            </div>
            <div class="hamburger" onclick="toggleSidebar()">
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
                <div class="hamburger-line"></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard-page.php"><i class="fas fa-home"></i> <span>Dashboard</span></a>
            <a href="user-page.php"><i class="fas fa-users"></i> <span>Users</span></a>
            <a href="inventory-page.php"><i class="fas fa-archive"></i> <span>Inventories</span></a>
            <a href="order-page.php"><i class="fas fa-box"></i> <span>Orders</span></a>
            <a href="bidding-record-page.php" class="active"><i class="fas fa-gavel"></i> <span>Bidding Records</span></a>
            <a href="sales-analysis.php"><i class="fas fa-chart-line"></i> <span>Sales Analysis</span></a>
            <a href="message-page.php"><i class="fas fa-inbox"></i> <span>Messages</span></a>
            <a href="banner-page.php"><i class="fas fa-ad"></i> <span>Banners</span></a>
            <a href="php-config/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Bidding Records Management</h1>
        </div>

        <table id="biddingRecordsTable" class="inventory-table">
            <thead>
                <tr>
                    <th>Auction ID</th>

                    <th>Product Name</th>
                    <th>Winner Bidder ID</th>
                    <th>Starting Bid (Rs.)</th>
                    <th>Ending Bid (Rs.)</th>
                    <th>Bidding Status</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT auctions.auction_id, auctions.product_id, auctions.winning_bidder_id, auctions.starting_bid, auctions.ending_bid,
                    auctions.start_time, auctions.end_time, auctions.is_end, product.product_name 
                    FROM auction_history as auctions
                    LEFT JOIN product ON auctions.product_id = product.product_id";

                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $auctionId = $row['auction_id'];
                        $productId = $row['product_id'];
                        $productName = $row['product_name'];
                        $winnerBidderId = $row['winning_bidder_id'] ?? "Not Decided";
                        $startingBid = $row['starting_bid'];
                        $endingBid = ($row['ending_bid'] === null || $row['ending_bid'] == 0) ? "Not Decided" : $row['ending_bid'];
                        $startTime = $row['start_time'];
                        $endTime = $row['end_time'];
                        $isEnd = $row['is_end'];

                        $currentDate = date('Y-m-d H:i:s');  

                        if ($isEnd) {
                            $status = "Ended";
                        } elseif ($currentDate < $startTime || (is_null($endTime))) {
                            $status = "Not Started";
                        } elseif ($currentDate >= $startTime && $currentDate <= $endTime) {
                            $status = "Ongoing";
                        } else {
                            $status = "Ended";
                        }

                        echo "<tr>
                        <td>$auctionId</td>
                        <td>$productName</td>
                        <td>$winnerBidderId</td>
                        <td>$startingBid</td>
                        <td>$endingBid</td>
                        <td>$status</td>
                        <td>" . date('Y-m-d', strtotime($startTime)) . "</td>
                        <td>" . (is_null($endTime) ? "Not Decided" : date('Y-m-d', strtotime($endTime))) . "</td>
                        <td>
                            <button class='action-buttons view-more-btn' data-auction-id='$auctionId'>View More</button>
                            <button class='action-buttons end-auction-btn' data-auction-id='$auctionId' data-product-id='$productId'" . ($status == 'Ended' ? ' disabled' : '') . ">End Auction</button>
                        </td>
                    </tr>";
                    }
                } else {
                    echo "<tr><td colspan='9'>No auctions found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="popup-overlay" id="biddingDetailsPopup">
        <div class="popup-content">
            <span class="close-btn" onclick="closeModal()">×</span>
            <h2 id="title-head">Bidding Records</h2>
            <table>
                <thead>
                    <tr>
                        <th>Bid ID</th>
                        <th>Bidder Name</th>
                        <th>Bid Amount</th>
                        <th>Bid Time</th>
                    </tr>
                </thead>
                <tbody id="biddingDetailsBody">
                    
                </tbody>
            </table>
        </div>
    </div>



    <script src="javascript/bidding-record-page.js"></script>
    <script src="javascript/side-navbar.js"></script>
</body>

</html>