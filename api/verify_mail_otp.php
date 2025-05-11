<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredOtp = $_POST['otp'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_SESSION['reset_email'];
    $role = $_SESSION['reset_role'];

    if ($password !== $confirm_password) {
        echo json_encode(['success' => false, 'message' => 'Passwords do not match.']);
        exit;
    }

    if ($enteredOtp == $_SESSION['reset_otp']) {
        // Assuming you have a database connection $conn
        $database = new Database();
        $db = $database->getConnection();
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        if ($role === 'customer') {
            $stmt = $db->prepare("UPDATE customers SET password = ? WHERE email = ?");
        } elseif ($role === 'driver') {
            $stmt = $db->prepare("UPDATE drivers SET password = ? WHERE email = ?");
        } elseif ($role === 'admin') {
            $stmt = $db->prepare("UPDATE admins SET password = ? WHERE email = ?");
        }

        if ($stmt) {
            $stmt->bind_param('ss', $hashed_password, $email);
            if ($stmt->execute()) {
                unset($_SESSION['reset_otp']);
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_role']);
                echo json_encode(['success' => true, 'message' => 'Password reset successfully.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to reset password.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid role.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>