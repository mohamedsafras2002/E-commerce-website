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



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['banner_image'])) {
    $bannerImage = $_FILES['banner_image'];
    $targetDir = "../images/slideshow-banner/";
    $targetFile = $targetDir . basename($bannerImage["name"]);
    $uploadOk = 1;

    $check = getimagesize($bannerImage["tmp_name"]);
    if ($check === false) {
        $uploadOk = 0;
        $error = "File is not an image.";
    }

    if (file_exists($targetFile)) {
        $uploadOk = 0;
        $error = "File already exists.";
    }

    if ($bannerImage["size"] > 5000000) { 
        $uploadOk = 0;
        $error = "File is too large.";
    }

    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif", "webp"])) {
        $uploadOk = 0;
        $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
    }

    if ($uploadOk === 1) {
        if (move_uploaded_file($bannerImage["tmp_name"], $targetFile)) {
            
            $query = "INSERT INTO banner (banner_image) VALUES ('$targetFile')";
            mysqli_query($conn, $query);
            $success = "Banner uploaded successfully!";
        } else {
            $error = "Error uploading the file.";
        }
    }
}

if (isset($_GET['delete_id'])) {
    $deleteId = $_GET['delete_id'];
    $query = "SELECT banner_image FROM banner WHERE banner_id = $deleteId";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        $imagePath = $row['banner_image'];
        
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        mysqli_query($conn, "DELETE FROM banner WHERE banner_id = $deleteId");
        $success = "Banner deleted successfully!";
    } else {
        $error = "Banner not found.";
    }
}

if (isset($_GET['toggle_activate_id'])) {
    $bannerId = $_GET['toggle_activate_id'];
   
    $query = "UPDATE banner SET is_activate = NOT is_activate WHERE banner_id = $bannerId";
    mysqli_query($conn, $query);
    $success = "Banner status updated successfully!";
    header("Location: banner-page.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banner Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/banner-page.css">
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
            <a href="banner-management.php"><i class="fas fa-ad"></i> <span>Banners</span></a>
            <a href="php-config/logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
        </nav>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Banner Management</h1>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger" id="error-message"><?php echo $error; ?></div>
        <?php elseif (isset($success)): ?>
            <div class="alert alert-success" id="success-message"><?php echo $success; ?></div>
        <?php endif; ?>

        <div class="upload-banner">
            <h2>Upload New Banner</h2>
            <form action="banner-page.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="banner_image" accept="image/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>

        <div class="banner-list">
            <h2>Active Banners</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Image</th>
                        <th>Uploaded At</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = "SELECT * FROM banner ORDER BY created_at DESC";
                    $result = mysqli_query($conn, $query);

                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>{$row['banner_id']}</td>";
                            echo "<td><img src='{$row['banner_image']}' alt='Banner' class='banner-thumbnail'></td>";
                            echo "<td>{$row['created_at']}</td>";
                            echo "<td>" . ($row['is_activate'] ? 'Active' : 'Inactive') . "</td>";
                            echo "<td>
                                <a href='banner-page.php?delete_id={$row['banner_id']}' class='delete-btn'>Delete</a>
                                <a href='banner-page.php?toggle_activate_id={$row['banner_id']}' class='toggle-status-btn'>
                                    " . ($row['is_activate'] ? 'Deactivate' : 'Activate') . "
                                </a>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No banners found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="javascript/side-navbar.js"></script>
    <script src="javascript/banner-page.js"></script>

</body>

</html>