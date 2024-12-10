<?php
include('php-config/ssession-config.php');
include('php-config/db-conn.php');

session_start();

$email = $_POST['email'];
$password = $_POST['password'];

$response = ['success' => false, 'message' => '', 'email_exists' => false];

if ($email && $password) {
    
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ? AND user_type_id = ? LIMIT 1");
    $user_type_id = 1;
    $stmt->bind_param('si', $email, $user_type_id);
    $stmt->execute();
    $result = $stmt->get_result();

    
    if ($result && $result->num_rows > 0) {
        $user_data = $result->fetch_assoc();
        $response['email_exists'] = true; 

        
        if (password_verify($password, $user_data['password'])) {
            
            $_SESSION['user_id'] = $user_data['user_id'];
            $_SESSION['name'] = $user_data['name'];

            
            // setcookie('user_id', $user_data['user_id'], time() + (86400 * 30), "/"); 
            // setcookie('user_name', $user_data['name'], time() + (86400 * 30), "/");

           
            $update_stmt = $conn->prepare("UPDATE user SET last_login = NOW() WHERE user_id = ?");
            $update_stmt->bind_param('i', $user_data['user_id']);
            $update_stmt->execute();

            
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
