<?php
include('php-config/db-conn.php');

session_start();

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message to Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/message-centre-page.css">
    <link rel="stylesheet" href="css/header.css">
</head>

<body>

    <nav>
        <div class="side-navbar" id="sideNavBar">
            <!-- <span class="close-btn" id="closeBtn">&times;</span> -->
            <img src="images/icons/close.svg" class="close-btn" id="closeBtn">
            <a href="index.php">
                <img class="sidebar-logo" src="images/logo/white-logo.png">
            </a>

            <div class="sidenav-category-section">
                <label for="categories" class="dropdown-label">Categories</label>
                <div class="dropdown-content">
                    <?php
                    // Initialize an array to store categories
                    $categories = [];

                    // Fetch all categories with their parent relationships
                    $result = $conn->query("SELECT category_id, category_name, parent_category_id FROM category ORDER BY parent_category_id, category_name");

                    // Organize categories into parent-child structure
                    while ($row = $result->fetch_assoc()) {
                        if ($row['parent_category_id'] === null) {
                            // Add parent category
                            $categories[$row['category_id']] = [
                                'name' => $row['category_name'],
                                'children' => []
                            ];
                        } else {
                            // Add child category under the respective parent
                            $categories[$row['parent_category_id']]['children'][] = [
                                'id' => $row['category_id'],
                                'name' => $row['category_name']
                            ];
                        }
                    }

                    // Display categories
                    foreach ($categories as $parent_id => $category): ?>
                        <a href="index.php?category_id=<?php echo $parent_id; ?>" class="subject parent-category" data-id="<?php echo $parent_id; ?>">
                            <div><?php echo htmlspecialchars($category['name']); ?></div>
                        </a>
                        <?php if (!empty($category['children'])): ?>
                            <div class="subcategory-content">
                                <?php foreach ($category['children'] as $child): ?>
                                    <a href="index.php?category_id=<?php echo $child['id']; ?>" class="subject child-category" data-id="<?php echo $child['id']; ?>" style="padding-left: 25px;">
                                        <div><?php echo htmlspecialchars($child['name']); ?></div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>




            <!-- <div class="sidenav-program-section">
                <label for="Programs & Events">Programs & Events</label>
                <a href="#" class="subject">
                    <div>All</div>
                </a>
                <a href="#" class="subject">
                    <div>Electrical</div>
                </a>
                <a href="#" class="subject">
                    <div>Home ware</div>
                </a>
                <a href="#" class="subject">
                    <div>Fashion</div>
                </a>
            </div> -->

            <div class="sidenav-setting-section">
                <label for="categories">Settings & Helps</label>


                <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="subject" id="greeting">
                        <span class="username">Hi,&#xA0;<?php echo $_SESSION['name']; ?></span>
                    </div>
                <?php else: ?>
                    <a href="signin-page.php" class="subject">
                        <div>Sign in</div>
                    </a>
                    <a href="register-page.html" class="subject">
                        <div>Register</div>
                    </a>
                <?php endif; ?>


                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="subject">
                        <div>Profile</div>
                    </a>
                    <a href="view-auction-records-page.php" class="subject">
                        <div>Auction History</div>
                    </a>
                    <a href="cart-page.php" class="subject">
                        <div>Cart</div>
                    </a>
                    <a href="order-page.php" class="subject">
                        <div>Orders</div>
                    </a>
                <?php else: ?>
                    <a href="signin-page.php" class="subject">
                        <div>Profile</div>
                    </a>
                    <a href="signin-page.php" class="subject">
                        <div>Auction History</div>
                    </a>
                    <a href="signin-page.php" class="subject">
                        <div>Cart</div>
                    </a>
                    <a href="signin-page.php" class="subject">
                        <div>Orders</div>
                    </a>
                <?php endif; ?>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="php-config/logout.php" class="subject">
                        <div>Log out</div>
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div id="overlay" class="overlay"></div>


        <div class="nav-bar">
            <div class="top-bar">
                <div class="left-section">
                    <button class="hamburger-button">
                        <img class="hamburger-menu" src="images/icons/hamburger-menu.png">
                    </button>

                    <a href="index.php">
                        <img class="comp-logo" src="images/logo/green-logo.png">
                    </a>
                </div>

                <form method="GET" action=" " class="mid-section">
                    <div class="mid-section">
                        <input class="search-bar" type="text" name="search" name="search" placeholder="Search" />
                        <button class="search-button">
                            <img src="images/icons/search.png">
                            <div class="tooltip">Search</div>
                        </button>
                    </div>
                </form>



                <div class="right-section">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="right-button" id="cart-button">
                            <a href="cart-page.php">
                                <img class="cart-icon" src="images/icons/cart.png">
                                <div class="tooltip">Cart</div>
                            </a>
                        </div>
                        <div class="right-button" id="order-button">
                            <a href="order-page.php">
                                <img class="order-icon" src="images/icons/order.png">
                                <div class="tooltip">Order</div>
                            </a>
                        </div>
                        <!-- <div class="right-button" id="notfication-button">
                            <a href="message-centre-page.php">
                                <img class="notfications-icon" src="images/icons/notification.png">
                                <div class="notfication-count">5</div>
                                <div class="tooltip">Notification</div>
                            </a>
                        </div> -->
                    <?php else: ?>
                        <div class="right-button" id="cart-button">
                            <a href="signin-page.php">
                                <img class="cart-icon" src="images/icons/cart.png">
                                <div class="tooltip">Cart</div>
                            </a>
                        </div>
                        <div class="right-button" id="order-button">
                            <a href="signin-page.php">
                                <img class="order-icon" src="images/icons/order.png">
                                <div class="tooltip">Order</div>
                            </a>
                        </div>
                        <!-- <div class="right-button" id="notfication-button">
                            <a href="signin-page.php">
                                <img class="notfications-icon" src="images/icons/notification.png">
                                <div class="notfication-count">5</div>
                                <div class="tooltip">Notification</div>
                            </a>
                        </div> -->
                    <?php endif; ?>

                    <div class="right-button" id="profileButton">
                        <?php
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }

                        $user = null;
                        if (isset($_SESSION['user_id'])) {
                            $userid = intval($_SESSION['user_id']);
                            $query = "SELECT * FROM user WHERE user_id = ?";
                            $stmt = $conn->prepare($query);
                            $stmt->bind_param("i", $userid);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            $user = $result->fetch_assoc();
                        }

                        // Determine profile picture path
                        $profilePicture = isset($_SESSION['user_id']) && !empty($user['profile_picture'])
                            ? "../images/user-dp/" . htmlspecialchars($user['profile_picture'], ENT_QUOTES, 'UTF-8')
                            : "images/icons/profile.png";
                        ?>
                        <img class="current-user-picture" src="<?php echo $profilePicture; ?>" onclick="toggleProfilePopupBox()">
                        <div class="profile-popup" id="profilePopup">

                            <div class="top-section">
                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <span class="username"><?php echo $user['name']; ?></span>
                                    <a href="php-config/logout.php">
                                        <button class="sigin-btn">Logout</button>
                                    </a>
                                <?php else: ?>
                                    <a href="signin-page.php">
                                        <button class="sigin-btn">Sign in</button>
                                    </a>
                                    <a class="register" href="register-page.html">Register</a>
                                <?php endif; ?>
                            </div>

                            <div class="profile-mid-section">

                                <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="profile.php">
                                        <img src="images/icons/profile-p.png">
                                        <div>Profile</div>
                                    </a>

                                    <a href="view-auction-records-page.php">
                                        <img src="images/icons/cart.png">
                                        <div>Auction History</div>
                                    </a>

                                    <a href="cart-page.php">
                                        <img src="images/icons/cart.png">
                                        <div>Cart</div>
                                    </a>
                                    <a href="order-page.php">
                                        <img src="images/icons/order.png">
                                        <div>Orders</div>
                                    </a>

                                    <a href="message-centre-page.php">
                                        <img src="images/icons/message-center.png">
                                        <div>Message Center</div>
                                    </a>
                                    <a href="#">
                                        <img src="images/icons/about-us.png">
                                        <div>About</div>
                                    </a>
                                <?php else: ?>
                                    <a href="signin-page.php">
                                        <img src="images/icons/profile-p.png">
                                        <div>Profile</div>
                                    </a>

                                    <a href="signin-page.php">
                                        <img src="images/icons/cart.png">
                                        <div>Auction History</div>
                                    </a>

                                    <a href="signin-page.php">
                                        <img src="images/icons/cart.png">
                                        <div>Cart</div>
                                    </a>
                                    <a href="signin-page.php">
                                        <img src="images/icons/order.png">
                                        <div>Orders</div>
                                    </a>

                                    <a href="signin-page.php">
                                        <img src="images/icons/message-center.png">
                                        <div>Message Center</div>
                                    </a>
                                    <a href="signin-page.php">
                                        <img src="images/icons/about-us.png">
                                        <div>About</div>
                                    </a>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bottom-bar">


                <!-- <div class="shortcut-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="#">
                            <div>Today's Deals</div>
                        </a>

                        <a href="#">
                            <div>Customer Service</div>
                        </a>

                    <?php else: ?>
                        <a href="signin-page.php">
                            <div>Today's Deals</div>
                        </a>

                        <a href="signin-page.php">
                            <div>Customer Service</div>
                        </a>
                    <?php endif; ?>

                    
                </div> -->

            </div>
        </div>
    </nav>

    <div class="main-content">
        <h1>Message Centre</h1>

        <!-- View Sent Messages & Admin Replies -->
        <div class="message-display">
            <h2>Your Messages</h2>
            <div class="message-list">
                <?php
                $userId = $_SESSION['user_id']; // Assuming user ID is stored in session

                // Get all messages where the buyer is the sender or the recipient
                $query = "SELECT * FROM message WHERE (sender_id = $userId OR reciver_id = $userId) ORDER BY created_at DESC";
                $result = mysqli_query($conn, $query);

                if ($result) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        $messageId = $row['message_id'];
                        $senderId = $row['sender_id'];
                        $reciverId = $row['reciver_id'];
                        $messageContent = $row['message_content'];
                        $adminReply = $row['reply_message']; // Admin's reply (could be NULL)
                        $createdAt = $row['created_at'];
                        $updatedAt = $row['updated_at']; // Fetch the updated_at field
                        $sender = ($senderId == $userId) ? "You" : "Admin"; // Check if the sender is the buyer or admin
                        $isSentByAdmin = ($senderId != $userId); // Admin sends a message to buyer

                        echo "<div class='message'>";
                        echo "<div class='message-header'>";
                        echo "<span class='sender'>$sender</span> <span class='timestamp'>$createdAt</span>";
                        echo "</div>";
                        echo "<div class='message-body'>$messageContent</div>";

                        // Display the reply message section
                        if ($adminReply) {
                            // Admin reply exists
                            echo "<div class='reply-section'>";
                            echo "<div class='message-header'><span class='sender'>Reply</span> <span class='timestamp'>$updatedAt</span></div>";
                            echo "<div class='message-body'>$adminReply</div>";
                            echo "</div>";
                        } else {
                            // If no admin reply, show "No reply"
                            echo "<div class='no-reply'>";
                            echo "<span>No reply from Admin</span>";
                            echo "</div>";
                        }

                        // If the message has a reply from the admin or was sent by admin, disable the reply button
                        if ($adminReply || $isSentByAdmin) {
                            echo "<button class='reply-btn' disabled>Replied</button>";
                        }

                        echo "</div>"; // End message div
                    }
                } else {
                    echo "<p>No messages found.</p>";
                }
                ?>
            </div>

        </div>

        <!-- Send a Message Form -->
        <div class="message-form">
            <h2>Send a Message</h2>
            <form action="send-message.php" method="POST">
                <textarea name="message" rows="4" placeholder="Type your message..." required></textarea>
                <button type="submit" class="send-btn">Send Message</button>
            </form>
        </div>
    </div>



    <script src="javascript/message-page.js"></script>
    <script src="javascript/header.js"></script>
</body>

</html>