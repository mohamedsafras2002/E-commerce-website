<?php
session_start();
include("php-config/db-conn.php");
include("php-config/ssession-config.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (isset($_POST['check_email'])) {
        $email = $_POST['check_email'];
        $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        echo json_encode(['exists' => $result->num_rows > 0]);
        exit;
    }

    $userType = $_POST['user-type'];
    $stmt = $conn->prepare("SELECT user_type_id FROM user_type WHERE type_name = ?");
    $stmt->bind_param("s", $userType);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userTypeId = $row['user_type_id'];
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User type not found.']);
        exit;
    }

    $fullname = $_POST['fName'] . ' ' . $_POST['lName'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $check = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($check);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists.']);
    } else {
        // Insert new user
        $query = "INSERT INTO user (user_type_id, name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $userTypeId, $fullname, $email, $password);

        if ($stmt->execute()) {
            // Get the inserted user ID
            $userId = $conn->insert_id;

            // Insert into buyer table if the user type is "buyer"
            if ($userType === 'buyer') {
                $buyerInsert = "INSERT INTO buyer (buyer_id) VALUES (?)";
                $stmtBuyer = $conn->prepare($buyerInsert);
                $stmtBuyer->bind_param("i", $userId);
                $stmtBuyer->execute();
                $stmtBuyer->close();
            }

            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
        }
    }

    $stmt->close();
    $conn->close();
}
?>
