<?php
include('php-config/db-conn.php');
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['admin_id'];
$adminName = isset($_SESSION['admin_name']) ? $_SESSION['admin_name'] : 'Admin';

$sql = "SELECT email FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$adminEmail = $result->fetch_assoc()['email'];


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_id'], $_POST['reply_message'])) {
    $messageId = $_POST['message_id'];
    $replyMessage = mysqli_real_escape_string($conn, $_POST['reply_message']);
    $updatedAt = date("Y-m-d H:i:s");

    $query = "UPDATE message SET reply_message = '$replyMessage' WHERE message_id = $messageId";
    if (mysqli_query($conn, $query)) {
        header("Location: message-page.php?reply_status=success");
    } else {
        header("Location: message-page.php?reply_status=error");
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/message-page.css">
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
            <a href="bidding-record-page.php"><i class="fas fa-gavel"></i> <span>Bidding Records</span></a>
            <a href="sales-analysis.php"><i class="fas fa-chart-line"></i> <span>Sales Analysis</span></a>
            <a href="message-page.php"><i class="fas fa-inbox"></i> <span>Messages</span></a>
            <a href="banner-page.php"><i class="fas fa-ad"></i> <span>Banners</span></a>
            <a href="php-config/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Message Management</h1>
        </div>


        <div class="messages-container">
            <table class="messages-table">
                <thead>
                    <tr>
                        <th>Message ID</th>
                        <th>User</th>
                        <th>Message Content</th>
                        <th>Admin Reply</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT m.*, u.name AS user_name FROM message m 
                              JOIN user u ON m.sender_id = u.user_id 
                              ORDER BY m.created_at DESC";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['message_id']}</td>";
                            echo "<td>{$row['user_name']}</td>";
                            echo "<td>{$row['message_content']}</td>";
                            echo "<td>" . ($row['reply_message'] ? $row['reply_message'] : "Need to reply") . "</td>";
                            echo "<td>{$row['created_at']}</td>";
                            echo "<td>" . ($row['updated_at'] ? $row['updated_at'] : "Not updated") . "</td>";
                            echo "<td>
                                <button class='reply-btn' onclick='showReplyForm({$row['message_id']}, \"{$row['message_content']}\", \"{$row['user_name']}\")' " .
                                ($row['reply_message'] ? "disabled" : "") . ">Reply</button>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No messages found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>


        <div class="popup-overlay" id="popupOverlay">
            <div class="popup-content">
                <span class="close-btn" onclick="hideReplyForm()">×</span>
                <h3 class="replyHead">Reply to Message</h3>
                <p class="userName">Buyer Name: <span id="userName"></span></p>
                <p class="messgContent">Message: <span id="userMessage"></span></p>
                <form class="formBox" action="message-page.php" method="POST">
                    <input type="hidden" name="message_id" id="replyMessageId">
                    <textarea id="replyTb" name="reply_message" placeholder="Type your reply here..." required></textarea>
                    <button type="submit" class="send-btn">Send Reply</button>
                </form>
            </div>
        </div>
    </div>


    <script src="javascript/side-navbar.js"></script>
    <script src="javascript/message-page.js"></script>

</body>

</html>