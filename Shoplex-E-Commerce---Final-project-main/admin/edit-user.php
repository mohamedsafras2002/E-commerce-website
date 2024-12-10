<?php
include 'php-config/db-conn.php';
include 'php-config/ssession-config.php';
session_start();


if (isset($_SESSION['admin_id'])) {
    $userId = $_SESSION['admin_id'];
    

    $sql = "SELECT name, email FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $adminEmail = $row['email'];
        $adminName = $row['name'];
    }
} else {
    header('Location: index.php');
}


if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die('User ID is missing.');
}

$userId = intval($_GET['user_id']); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE user SET name = ?, email = ?,  password = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssi', $name, $email, $hashedPassword, $userId);
    } else {
        $sql = "UPDATE user SET name = ?, email = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssi', $name, $email, $userId);
    }

    if ($stmt->execute()) {
        header('Location: user-page.php');
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$sql = "SELECT user_id, name, email, user_type_id FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    die('User not found. Please check if the user ID is valid.');
}

$sqlUserTypes = "SELECT user_type_id, type_name FROM user_type";
$resultUserTypes = $conn->query($sqlUserTypes);

if (isset($_GET['check_email']) && isset($_GET['email'])) {
    $email = $_GET['email'];
    $sql = "SELECT 1 FROM user WHERE email = ? AND user_id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $email, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $exists = $result->num_rows > 0;
    echo json_encode(['exists' => $exists]);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="css/edit-userpage.css">
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

    <div class="container">
        <h2>Edit User</h2>
        <form action="" method="POST">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            <br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            <small id="email-error" style="color: red; display: none;">This email is already in use.</small>
            <br>


            <label for="password">Password (leave blank to keep unchanged):</label>
            <input type="password" id="password" name="password">
            <br>

            <button type="submit">Update</button>
            <a href="user-page.php" class="cancel-btn">Cancel</a>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const emailInput = document.getElementById('email');
            const emailError = document.getElementById('email-error');
            const userId = <?php echo $userId; ?>;

            emailInput.addEventListener('input', function() {
                const email = emailInput.value.trim();

                if (email !== '') {
                    fetch(`edit-user.php?check_email=1&email=${encodeURIComponent(email)}&user_id=${userId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.exists) {
                                emailError.style.display = 'inline'; 
                            } else {
                                emailError.style.display = 'none'; 
                            }
                        })
                        .catch(error => {
                            console.error('Error checking email:', error);
                        });
                } else {
                    emailError.style.display = 'none'; 
                }
            });
        });
    </script>

    <script src="javascript/edit-user.js"></script>
    <script src="javascript/side-navbar.js"></script>

</body>

</html>

<?php
$stmt->close();
$conn->close();
?>