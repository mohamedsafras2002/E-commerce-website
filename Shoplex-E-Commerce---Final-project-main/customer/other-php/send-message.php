<?php
include '../php-config/db-conn.php';
session_start();

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $userId = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Insert message into the database
    $query = "INSERT INTO message (sender_id, reciver_id, message_content, message_type) 
              VALUES ('$userId', NULL, '$message', 'normal')";
    
    if (mysqli_query($conn, $query)) {
        $response['success'] = true; // If the message is sent successfully
    }
}

echo json_encode($response); // Return the response as JSON
?>
