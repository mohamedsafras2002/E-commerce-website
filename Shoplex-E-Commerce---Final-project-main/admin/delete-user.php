<?php
include 'php-config/db-conn.php';
include 'php-config/ssession-config.php';
session_start();

// Check if a user ID is provided
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    die('User ID is missing.');
}

$userId = intval($_GET['user_id']);

// Delete the user from the database
$sql = "DELETE FROM user WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $userId);

if ($stmt->execute()) {
    header('Location: user-page.php'); // Redirect to user-management after editing
    exit;
    
} else {
    echo "Error deleting record: " . $conn->error;
}

$stmt->close();
$conn->close();
?>