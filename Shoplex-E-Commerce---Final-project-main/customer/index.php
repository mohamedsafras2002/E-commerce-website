<?php
include('php-config/db-conn.php');
// ini_set('session.cookie_lifetime', 60 * 60 * 24 * 365);
// ini_set('session.gc-maxlifetime', 60 * 60 * 24 * 365);
session_start();
error_reporting(E_ALL);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shoplex - All Your Favourites in One Place</title>

    <link rel="icon" type="image/png" href="images/favicon/favicon-48x48.png" sizes="48x48" />
    <link rel="icon" type="image/svg+xml" href="images/favicon/favicon.svg" />
    <link rel="shortcut icon" href="images/favicon/favicon.ico" />
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon/apple-touch-icon.png" />
    <link rel="manifest" href="images/favicon/site.webmanifest" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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
        <?php
        $sql = "SELECT banner_image FROM banner WHERE is_activate = 1";
        $result = $conn->query($sql);
        ?>
        <section class="slideshow">
            <div class="arrow-back">
                <img src="images/icons/arrow-back.png" class="arrow" alt="Previous">
            </div>
            <div class="slideshow-image-box">
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <div class="slide">
                            <img src="<?php echo htmlspecialchars($row['banner_image']); ?>" alt="Slide">
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>No banners available.</p>
                <?php endif; ?>
            </div>
            <div class="arrow-forward">
                <img src="images/icons/arrow-forward.png" class="arrow" alt="Next">
            </div>
        </section>

        <section class="for-you-products-section">
            <div class="list-type">
                <h1>For You</h1>
            </div>
            <div class="products-grid">
                <?php
                $searchTerm = isset($_GET['search']) ? $_GET['search'] : '';
                $categoryId = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null;

                if ($categoryId) {
                    $categories = [$categoryId];
                    $categoryQuery = "SELECT category_id FROM category WHERE parent_category_id = ?";
                    $stmt = $conn->prepare($categoryQuery);

                    $queue = [$categoryId];
                    while (!empty($queue)) {
                        $currentCategory = array_shift($queue);
                        $stmt->bind_param("i", $currentCategory);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        while ($row = $result->fetch_assoc()) {
                            $categories[] = $row['category_id'];
                            $queue[] = $row['category_id'];
                        }
                    }

                    $placeholders = implode(',', array_fill(0, count($categories), '?'));
                    $sql = "SELECT * FROM product WHERE category_id IN ($placeholders)";
                    $stmt = $conn->prepare($sql);

                    $types = str_repeat('i', count($categories));
                    $stmt->bind_param($types, ...$categories);
                } else {
                    $sql = "SELECT * FROM product WHERE product_name LIKE ? OR description LIKE ?";
                    $stmt = $conn->prepare($sql);
                    $searchTermWithWildcard = "%" . $searchTerm . "%";
                    $stmt->bind_param("ss", $searchTermWithWildcard, $searchTermWithWildcard);
                }

                $stmt->execute();
                $productsResult = $stmt->get_result();

                if ($productsResult->num_rows > 0) {
                    while ($row = $productsResult->fetch_assoc()) {
                ?>

                        <div class="product-card">
                            <p class="product-id" hidden><?php $row["product_id"]; ?></p>

                            <div class="product-image">
                                <?php
                                $productId = (int)$row["product_id"];
                                $sql = "SELECT picture_path FROM product_picture WHERE product_id = $productId AND default_picture = 1";

                                $productPictureResult = $conn->query($sql);


                                if ($productPictureResult && $productPictureResult->num_rows > 0) {

                                    $pictureRow = $productPictureResult->fetch_assoc();
                                    $picturePath = $pictureRow['picture_path'];
                                } else {

                                    $picturePath = 'images\product-images\no_picture.jpg';
                                }
                                ?>
                                <img src="<?php echo "../" . $picturePath; ?>" alt="Product Image" class="product-img">
                            </div>
                            <div class="product-details">

                                <h2 class="product-name"><?php echo $row["product_name"]; ?></h2>
                                <div class="rating">
                                    <?php
                                    $productId = (int)$row["product_id"];
                                    $sql = "SELECT * FROM product_review WHERE product_id = $productId";

                                    $reviewResult = $conn->query($sql);


                                    $totalRating = 0;
                                    $count = 0;


                                    if ($reviewResult && $reviewResult->num_rows > 0) {
                                        while ($reviewRow = $reviewResult->fetch_assoc()) {
                                            $totalRating += (int)$reviewRow["rating"];
                                            $count++;
                                        }

                                        $productRating = $totalRating / $count;

                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= $productRating) {
                                                echo '<span class="fa fa-star checked"></span>';
                                            } else {
                                                echo '<span class="fa fa-star"></span>';
                                            }
                                        }
                                        echo '<span class="rating-point">(' . number_format($productRating, 1) . ')</span>';
                                        echo '<span class="review-count">(' . $count . ' reviews)</span>';
                                    } else {
                                        echo '
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="fa fa-star"></span>
                                            <span class="rating-point">(0.0)</span>
                                            <span class="review-count">(0 reviews)</span>
                                        ';
                                    }
                                    ?>
                                </div>
                                <div class="price">
                                    <?php
                                    if ((int)$row["bid_activate"] === 1) {
                                        // If it's a bid product, show bid starting price
                                        echo '<span class="bid-starting-price price-label">Starting Bid: </span>';
                                        echo '<span class="bid-starting-price bid-price">LKR. ' . number_format((float)$row["bid_starting_price"], 2) . '</span>';
                                    } else {
                                        // If it's a regular product, show discounted price and original price
                                        $discount = (float)$row["discount"] > 0 ? (float)$row["discount"] : 0;
                                        $price = (float)$row["price"];
                                        $discountPrice = $price - ($price * $discount);

                                        echo '<span class="discounted-price">LKR. ' . number_format($discountPrice, 2) . '</span>';

                                        if ($discount > 0) {
                                            echo '<span class="original-price">LKR. ' . number_format($price, 2) . '</span>';
                                            echo '<span class="discount-badge">' . ($discount * 100) . '% off</span>';
                                        }
                                    }
                                    ?>
                                </div>


                                <div class="shipping">
                                    <span>
                                        <?php
                                        if ((float)$row["shipping_fee"] > 0) {
                                            echo "Shipping Fee: LKR. " . $row["shipping_fee"];
                                        } else {
                                            echo "Free Shipping";
                                        }

                                        ?>
                                    </span>
                                </div>
                                <div class="stock-status">
                                    Availability:
                                    <span>
                                        <?php
                                        $stock = (int)$row["stock"];

                                        if ($stock > 10) {
                                            echo "In Stock";
                                        } elseif ($stock > 0) {
                                            echo "Only $stock left in stock!";
                                        } else {
                                            echo "Out of Stock";
                                        }
                                        ?>
                                    </span>
                                </div>


                                <div class="buttons">

                                    <?php
                                    if ((int)$row["bid_activate"] == 1) {
                                        echo '<button id="placeBidBtn" class="place-bid" style="display: block;"data-product-id="' . $row['product_id'] . '">Place Bid</button>';
                                        echo '<button class="add-to-cart" style="display: none;" data-product-id="' . $row['product_id'] . '">Add to Cart</button>';
                                        echo '<button class="buy-now" style="display: none;" data-product-id="' . $row['product_id'] . '">Buy Now</button>';
                                    } else {
                                        echo '<button class="add-to-cart" data-product-id="' . $row['product_id'] . '">Add to Cart</button>';
                                        echo '<button id="addToCartBtn" class="buy-now" data-product-id="' . $row['product_id'] . '">Buy Now</button>';
                                        echo '<button id="buyNowBtn" class="place-bid" style="display: none;" data-product-id="' . $row['product_id'] . '">Place Bid</button>';
                                    }
                                    ?>


                                </div>

                            </div>

                        </div>

                <?php
                    }
                } else {
                    echo "No products found.";
                }
                ?>



                <div id="product-preview-modal" class="modal">
                    <div class="modal-content">

                        <span class="close-button">&times;</span>

                        <div class="modal-body">

                            <div class="modal-product-details">
                                <div class="modal-left">
                                    <div id="carousel-container">
                                        <div class="carousel"></div>
                                        <button id="prev-btn" class="carousel-btn">&#8249;</button>
                                        <button id="next-btn" class="carousel-btn">&#8250;</button>
                                    </div>
                                </div>

                                <div class="modal-middle">
                                    <h2 id="modal-product-name">Product Name</h2>
                                    <div id="modal-product-description"></div>

                                    <div id="modal-product-rating"></div>

                                    <div class="price">
                                        <span id="modal-discounted-price">LKR. 1000</span>
                                        <span id="modal-original-price">LKR. 1500</span>
                                        <span id="modal-discount-badge">20% off</span>
                                        <div class="bid-price-box">
                                            <span id="modal-bid-starting-price">LKR. 1000</span>
                                            <span id="modal-highest-bid-price">Highest Bid Amount: LKR. <span class="highest-bid-price"></span></span>
                                        </div>

                                    </div>

                                    <div class="stock-shipping">
                                        <p class="stock-info" id="modal-stock-info">Stock: 10 available</p>
                                        <p class="shipping-info" id="modal-shipping-info">Shipping Fee: LKR. 200</p>
                                    </div>

                                    <div class="quantity-controller">
                                        <button id="decrease-quantity">-</button>
                                        <input type="text" id="quantity-input" value="1" min="1" disabled />
                                        <button id="increase-quantity">+</button>
                                    </div>

                                    <div class="place-bid-price-controller">
                                        <p id="auction-id" hidden></p>
                                        <div class="controller-box">
                                            <label>Place bid price: </label>
                                            <button id="decrease-bid-price">-</button>
                                            <input type="text" id="place-bid-price-input" />
                                            <button id="increase-bid-price">+</button>
                                        </div>
                                        <div class="bid-ending-date-box">Ending date:
                                            <span id="modal-bid-ending_date"></span>
                                        </div>
                                        <div class="bid-status-box">Status:
                                            <span id="modal-bid-status"></span>
                                        </div>

                                    </div>


                                    <!-- Action Buttons -->
                                    <div class="action-buttons">
                                        <button id="custom-add-to-cart" class="custom-button">Add to Cart</button>
                                        <button id="custom-buy-now" class="custom-button custom-buy-now">Buy Now</button>
                                        <button id="custom-place-bid" class="custom-button">Place Bid</button>
                                    </div>

                                </div>
                            </div>

                            <div class="modal-product-review">
                                <h3>Customer Reviews</h3>
                                <h3 id="no-review-label">No reviews</h3>
                                <div id="reviews-container">

                                    <!-- Example Review -->
                                    <!-- <div class="review-item">
                                        <p class="reviewer-name">John Doe</p>
                                        <div class="review-rating">★★★★☆</div>
                                        <p class="review-content">This product exceeded my expectations! Highly recommend.</p>
                                    </div> -->
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <div id="orderConfirmationModal" class="order-modal">
                    <div class="modal-content">
                        <h2>Confirm Your Order</h2>
                        <p><strong>Quantity:</strong> <span id="modal-quantity-info"></span></p>
                        <p><strong>Price After Discount:</strong> LKR. <span id="modal-price-info"></span></p>
                        <p><strong>Shipping Fee:</strong> LKR. <span id="modal-shipping-fee-info"></span></p>
                        <p><strong>Total:</strong> LKR. <span id="modal-total-info"></span></p>
                        <button id="confirm-order-btn" class="confirm-button">Confirm Order</button>
                        <button id="cancel-order-btn" class="cancel-button">Cancel</button>
                    </div>
                </div>
                <div id="success-message" class="success-message">
                    <p>Order placed successfully!</p>
                </div>


            </div>
        </section>

    </main>





    <script src="javascript/header.js"></script>
    <script src="javascript/signin-validation.js"></script>
    <script src="javascript/slideshow.js"></script>
    <script src="javascript/product-preview.js"></script>

</body>

</html>