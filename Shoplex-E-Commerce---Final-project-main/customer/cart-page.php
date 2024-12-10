<?php
include('php-config/db-conn.php');
include('php-config/ssession-config.php');

session_start();
if (!isset($_SESSION['user_id'])) {
    die("Please log in to access your cart.");
}

$userId = $_SESSION['user_id'];

// Fetch cart ID for the logged-in user
$cartSql = "SELECT cart_id FROM cart WHERE buyer_id = ?";
$cartStmt = $conn->prepare($cartSql);
$cartStmt->bind_param('i', $userId);
$cartStmt->execute();
$cartResult = $cartStmt->get_result();

if ($cartResult->num_rows === 0) {
    echo "<p>Your cart is empty.</p>";
    exit;
}

$cart = $cartResult->fetch_assoc();
$cart_id = $cart['cart_id'];

// Fetch cart items
$cartItemSql = "SELECT ci.*, p.product_name, p.price, p.discount, p.shipping_fee 
                FROM cart_item ci
                JOIN product p ON ci.product_id = p.product_id
                WHERE ci.cart_id = ?";
$cartItemStmt = $conn->prepare($cartItemSql);
$cartItemStmt->bind_param('i', $cart_id);
$cartItemStmt->execute();
$cartItemResult = $cartItemStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="css/carts.css">
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


    <main>
        <h2>Cart</h2>
        <?php if ($cartItemResult->num_rows > 0): ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (LKR.)</th>
                        <th>Discount (%)</th>
                        <th>Quantity</th>
                        <th>Shipping Fee (LKR.)</th>
                        <th>Subtotal (LKR.)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grandTotal = 0;
                    while ($item = $cartItemResult->fetch_assoc()):
                        $discountedPrice = $item['price'] - ($item['price'] * $item['discount']);
                        $subtotal = $discountedPrice * $item['quantity'];
                        $grandTotal += $subtotal + $item['shipping_fee'];
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td>LKR. <?= number_format($item['price'], 2) ?></td>
                            <td><?= $item['discount'] * 100 ?>%</td>
                            <td>
                                <form method="POST" action="other-php/update-cart.php">
                                    <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                                    <input type="number" name="quantity" value="<?= $item['quantity'] ?>" min="1">
                                    <button type="submit">Update</button>
                                </form>
                            </td>
                            <td>LKR <?= number_format($item['shipping_fee'], 2) ?></td>
                            <td>LKR <?= number_format($subtotal, 2) ?></td>
                            <td>
                                <form method="POST" action="other-php/remove-cart.php">
                                    <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                                    <button type="submit">Remove</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5"><strong>Grand Total</strong></td>
                        <td colspan="2"><strong>LKR <?= number_format($grandTotal, 2) ?></strong></td>
                    </tr>
                </tfoot>
            </table>
            <form method="POST" action="other-php/checkout.php">
                <button type="submit">Checkout</button>
            </form>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </main>

    <script src="javascript/header.js"></script>
</body>

</html>