<?php

function check_login($conn)
{

	if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];
        $user_type_id = 1; 

        $query = "SELECT * FROM user WHERE user_id = ? AND user_type_id = ? LIMIT 1";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            die("Failed to prepare statement: " . $conn->error);
        }
        $stmt->bind_param('ii', $user_id, $user_type_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
    }

	header("Location: ../signin_page.php");
	die;

}

