<?php
include('php-config/ssession-config.php');
include('php-config/db-conn.php');

session_start(); // Start the session

$email = $_POST['email'] ?? null;
$password = $_POST['password'] ?? null;

// Initialize the response array
$response = ['success' => false, 'message' => '', 'email_exists' => false];

if ($email && $password) {
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND user_type_id = ? LIMIT 1");
    $admin_user_type_id = 4; // Adjust this as per your database structure
    $stmt->bind_param('si', $email, $admin_user_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $response['email_exists'] = true;

        if (password_verify($password, $user_data['password'])) {
            // Debug: Verify session handling
            if (!isset($_SESSION)) {
                $response['message'] = 'Session not started';
                echo json_encode($response);
                exit;
            }

            // Set session variables
            $_SESSION['admin_id'] = $user_data['user_id'];
            $_SESSION['admin_name'] = $user_data['name'];
            $_SESSION['is_admin'] = true;

            // Debug: Check if session variables are set
            if (!isset($_SESSION['admin_id']) || !isset($_SESSION['admin_name'])) {
                $response['message'] = 'Failed to set session variables';
                echo json_encode($response);
                exit;
            }

            // Update the last login timestamp
            $update_stmt = $conn->prepare("UPDATE user SET last_login = NOW() WHERE user_id = ?");
            $update_stmt->bind_param('i', $user_data['user_id']);
            $update_stmt->execute();

            $response['is_admin'] = true;
            $response['success'] = true;
            $response['user_id'] = $user_data['user_id'];
            $response['user_name'] = $user_data['name'];
        } else {
            $response['message'] = 'Incorrect password';
        }
    } else {
        $response['message'] = 'Email not found';
    }
} else {
    $response['message'] = 'Missing email or password';
}

echo json_encode($response);
exit;
?>
