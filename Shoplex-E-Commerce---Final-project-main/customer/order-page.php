<?php
include('php-config/db-conn.php');
session_start();
error_reporting(E_ALL);
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Page - Shoplex</title>
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="css/styles.css">
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

    <div class="container">
        <h1>Auction History</h1>
        <?php
        include('php-config/db-conn.php');
        include('php-config/ssession-config.php');



        $userId = $_SESSION['user_id'];


        $orderSql = "SELECT o.order_id, o.ordered_at, o.total_amount, o.total_shipping_fee, oi.order_item_id, oi.product_id, oi.quantity, oi.price_after_discount, oi.subtotal, oi.shipping_fee, oi.status_id, oi.shipped_date, oi.expected_delivery_date, oi.delivered_date, p.product_name, s.status_name
                 FROM orders o
                 JOIN order_item oi ON o.order_id = oi.order_id
                 JOIN product p ON oi.product_id = p.product_id
                 JOIN order_status s ON oi.status_id = s.status_id
                 WHERE o.buyer_id = ? ORDER BY o.ordered_at DESC";
        $orderStmt = $conn->prepare($orderSql);
        $orderStmt->bind_param('i', $userId);
        $orderStmt->execute();
        $orderResult = $orderStmt->get_result();

        $orders = [];

        while ($order = $orderResult->fetch_assoc()) {
            $orders[$order['order_id']]['order_info'] = $order;
            $orders[$order['order_id']]['items'][] = $order;
        }

        if (count($orders) > 0) {
            echo '<div class="order-container">';

            foreach ($orders as $orderData) {
                $order = $orderData['order_info'];
                echo '<div class="order-card">';
                echo '<h3>Order ID: ' . htmlspecialchars($order['order_id']) . '</h3>';
                echo '<p>Order Date: ' . htmlspecialchars($order['ordered_at']) . '</p>';
                echo '<p>Total Amount: LKR ' . number_format($order['total_amount'], 2) . '</p>';
                echo '<p>Shipping Fee: LKR ' . number_format($order['total_shipping_fee'], 2) . '</p>';

                echo '<table class="order-items-table">';
                echo '<thead>';
                echo '<tr>';
                echo '<th>Product Name</th>';
                echo '<th>Quantity</th>';
                echo '<th>Price After Discount</th>';
                echo '<th>Subtotal</th>';
                echo '<th>Shipping Fee</th>';
                echo '<th>Status</th>';
                echo '<th>Shipped Date</th>';
                echo '<th>Expected Delivery</th>';
                echo '<th>Delivered Date</th>';
                echo '<th>Action</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';

                foreach ($orderData['items'] as $item) {
                    $subtotal = $item['quantity'] * $item['price_after_discount'];
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($item['product_name']) . '</td>';
                    echo '<td>' . htmlspecialchars($item['quantity']) . '</td>';
                    echo '<td>LKR ' . number_format($item['price_after_discount'], 2) . '</td>';
                    echo '<td>LKR ' . number_format($subtotal, 2) . '</td>';
                    echo '<td>LKR ' . number_format($item['shipping_fee'], 2) . '</td>';
                    echo '<td>' . htmlspecialchars($item['status_name']) . '</td>';
                    echo '<td>' . ($item['shipped_date'] ? htmlspecialchars($item['shipped_date']) : 'Not Shipped') . '</td>';
                    echo '<td>' . ($item['expected_delivery_date'] ? htmlspecialchars($item['expected_delivery_date']) : 'N/A') . '</td>';
                    echo '<td>' . ($item['delivered_date'] ? htmlspecialchars($item['delivered_date']) : 'Not Delivered') . '</td>';
                    echo '<td>';

                    if ($item['status_name'] !== 'Shipped') {
                        echo '<button class="confirm-delivery-btn" id="button-' . $item['order_item_id'] . '" style="display:none;">Confirm Delivered</button>';
                    } else {
                        echo '<button class="confirm-delivery-btn" id="button-' . $item['order_item_id'] . '" onclick="confirmDelivered(' . $item['order_item_id'] . ')">Confirm Delivered</button>';
                    }


                    echo '</td>';
                    echo '</tr>';
                }


                echo '</tbody>';
                echo '</table>';
                echo '</div>';
            }

            echo '</div>';
        } else {
            echo '<p>You have no orders.</p>';
        }

        $orderStmt->close();
        $conn->close();
        ?>
    </div>

    <!-- Modal Popup for Review and Rating -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close-btn" id="closeModal">&times;</span>
            <h2>Write a Review</h2>
            <form id="reviewForm">
                <label for="rating">Rating:</label>
                <select id="rating" name="rating" required>
                    <option value="">Select a rating</option>
                    <option value="5">5 - Excellent</option>
                    <option value="4">4 - Good</option>
                    <option value="3">3 - Average</option>
                    <option value="2">2 - Poor</option>
                    <option value="1">1 - Terrible</option>
                </select>

                <label for="review">Review:</label>
                <textarea id="review" name="review" placeholder="Write your review here..." rows="4"
                    required></textarea>

                <button type="submit">Submit Review</button>
            </form>
        </div>
    </div>



    <script src="javascript/header.js"></script>
    <script src="javascript/order-view-page.js"></script>
</body>

</html>