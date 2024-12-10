<?php
include 'php-config/ssession-config.php';
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


if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    echo json_encode(['exists' => $result->num_rows > 0]);

    $stmt->close();
    $conn->close();
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_type_id = $_POST['user_type_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $profile_picture = $_FILES['profile_picture']['name'];


    $target_dir = "../images/user-dp/"; // Ensure this directory exists and is writable
    $target_file = $target_dir . basename($profile_picture);

    if (!empty($profile_picture)) {
        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file)) {
            $profile_picture_path = basename($profile_picture); // Full path to the uploaded file
        } else {
            // Handle file upload error
            $profile_picture_path = null;
            error_log("Failed to upload profile picture: " . $_FILES['profile_picture']['error']);
        }
    } else {
        $profile_picture_path = null; // No file uploaded
    }


    $sql = "INSERT INTO user (user_type_id, name, email, password, profile_picture) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issss", $user_type_id, $name, $email, $password, $profile_picture_path);

    if ($stmt->execute()) {
        // Fetch the last inserted user_id
        $inserted_user_id = $conn->insert_id;

        if ($user_type_id == 1) {
            // Insert the user_id into the buyer table as buyer_id
            $buyer_sql = "INSERT INTO buyer (buyer_id) VALUES (?)";
            $buyer_stmt = $conn->prepare($buyer_sql);
            $buyer_stmt->bind_param("i", $inserted_user_id);

            if ($buyer_stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'User added successfully and buyer record created!', 'redirect' => 'user-page.php']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'User added but error creating buyer record: ' . $buyer_stmt->error]);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'User added successfully!', 'redirect' => 'user-page.php']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error adding user: ' . $stmt->error]);
    }



    $stmt->close();
    $conn->close();
    exit();
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/add-user.css">
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

    <div class="form-container">
        <h2>Add New User</h2>
        <form action="add-user.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="user_type_id">User Type:</label>
                <select name="user_type_id" id="user_type_id" required>
                    <option value="4">Admin</option>
                    <option value="1">Buyer</option>
                </select>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="profile_picture">Profile Picture:</label>
                <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
            </div>
            <button type="submit" class="submit-btn">Add User</button>
        </form>
        <a href="user-page.php" class="back-btn">Back to User Management</a>
    </div>

    <script type="module" src="javascript/add-user.js"></script>
    <script src="javascript/side-navbar.js"></script>
</body>

</html>