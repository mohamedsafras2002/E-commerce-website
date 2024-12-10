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
    <title>Inventory Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/inventory-page.css">

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
            <h1>Inventory Management</h1>
            <div>
                <button id="addProductBtn" class="inventory-buttons">Add Product</button>
            </div>
        </div>

        <div>
            <button class="inventory-buttons" id="normalProductBtn">Normal Products</button>
            <button class="inventory-buttons" id="biddingProductBtn">Bidding Products</button>
        </div>
        
        <table id="productsTable" class="inventory-table">
            <thead>
                <tr id="tableHeaders">
                    
                </tr>
            </thead>
            <tbody id="productsTableBody">
                
            </tbody>
        </table>

        <div class="popup-overlay" id="productPopup" >
            <div class="popup-content">
                <span class="close-btn" onclick="closeProductForm()">&times;</span>

                <div class="product-type-selector">
                    <button class="product-type-btn active" data-type="normal" onclick="selectProductType('normal')">Normal Product</button>
                    <button class="product-type-btn" data-type="bidding" onclick="selectProductType('bidding')">Bidding Product</button>
                </div>

                <form id="productForm">
                    <div class="form-section">
                        <label>Product Name</label>
                        <input type="text" name="productName">
                    </div>

                    <div class="form-section">
                        <label>Description</label>
                        <textarea rows="4" name="description"></textarea>
                    </div>

                    <div class="form-section">
                        <label for="parentCategory">Parent Category</label>
                        <select id="parentCategory" name="parentCategory" onchange="fetchSubCategories(this.value)">
                            <option value="">Select Parent Category</option>
                        </select>
                    </div>

                    <div class="form-section">
                        <label for="subCategory">Sub Category</label>
                        <select id="subCategory" name="subCategory" disabled>
                            <option value="">Select Sub Category</option>
                        </select>
                    </div>





                    <div id="normalProductFields">
                        <div class="form-section">
                            <label>Price</label>
                            <input type="number" min="1" step="0.01" name="price">
                        </div>

                        <div class="form-section">
                            <label>Discount (%)</label>
                            <input type="number" min="0" max="100" name="discount" value="0">
                        </div>

                    </div>

                    <div id="biddingProductFields" style="display:none;">
                        <div class="form-section">
                            <label>Bid Starting Price</label>
                            <input type="number" name="bidStartingPrice" min="1" step="0.01">
                        </div>

                        <div class="form-section">
                            <label>Bid Starting Date</label>
                            <input type="date" name="bidStartDate">
                        </div>

                        <div class="form-section">
                            <label>Bid Ending Date</label>
                            <input type="date" name="bidEndDate">
                        </div>
                    </div>

                    <div class="form-section">
                        <label>Stock</label>
                        <input type="number" min="1" value="1" name="stock">
                    </div>

                    <div class="form-section">
                        <label>Shipping Fee</label>
                        <input type="number" name="shippingFee" min="0" value="0" step="0.01">
                    </div>

                    <div class="form-section">
                        <label>Product Images</label>
                        <div id="imageUploadContainer" class="image-upload">
                            <input type="file" id="imageInput" accept="image/*" multiple hidden>
                            <p>Click to upload images or drag and drop here</p>
                        </div>
                        <div id="imagePreviewContainer" class="image-preview-container"></div>
                        <div id="productImagesContainer"></div>
                    </div>
                    <input type="hidden" id="productId" name="productId" value="existingProductIdHere" style="display: none;">
                    <input type="hidden" name="productType" id="productTypeHidden" />


                    <button type="submit" class="submit-btn" id="addProductSubmitBtn">Add Product</button>
                    <button type="submit" class="submit-btn" id="updateSubmitBtn" style="display: none;">Update</button>
                </form>
            </div>
        </div>

        <div id="success-message" class="success-message">
            <p>Order placed successfully!</p>
        </div>

    </div>



    <script src="javascript/user-page.js"></script>
    <script src="javascript/inventory-page.js"></script>
    <script src="javascript/side-navbar.js"></script>
</body>

</html>