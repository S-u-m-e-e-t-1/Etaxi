<?php
require '../libs/smtp/smtp/PHPMailerAutoload.php';
require '../includes/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $role = $_POST['role'];
    $otp = rand(100000, 999999);
    $_SESSION['reset_otp'] = $otp;
    $_SESSION['reset_email'] = $email;
    $_SESSION['reset_role'] = $role;

    // Assuming you have a database connection $conn
    $database = new Database();
    $db = $database->getConnection();

    $userExists = false;

    if ($role === 'customer') {
        $stmt = $db->prepare("SELECT email FROM customers WHERE email = ?");
    } elseif ($role === 'driver') {
        $stmt = $db->prepare("SELECT email FROM drivers WHERE email = ?");
    } elseif ($role === 'admin') {
        $stmt = $db->prepare("SELECT email FROM admins WHERE email = ?");
    }

    if ($stmt) {
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $userExists = true;
        }
    }

    if ($userExists) {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->SMTPAuth = true;
            $mail->SMTPSecure = 'tls';
            $mail->Host = "smtp.gmail.com";
            $mail->Port = 587;
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';

            $mail->Username = "sumeetpanigrahy494@gmail.com";
            $mail->Password = "wlsl gnsq sbbw tldz";
            $mail->SetFrom("sumeetpanigrahy494@gmail.com", "Etaxi Service");

            // Recipients
            $mail->addAddress($email);

            // Content
            $mail->Subject = 'Password Reset OTP';
            $mail->Body    = "Your OTP code is $otp";

            $mail->send();
            echo json_encode(['success' => true, 'message' => 'OTP sent to your email.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Email not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>