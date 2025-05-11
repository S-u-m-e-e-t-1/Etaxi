<?php
// filepath: c:\xampp\htdocs\ETaxi\api\verify_otp.php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $enteredOtp = $_POST['otp'];
    $storedOtp = isset($_SESSION['otp']) ? $_SESSION['otp'] : null;
    $storedPhone = isset($_SESSION['otp_phone']) ? $_SESSION['otp_phone'] : null;
    $enteredPhone = isset($_POST['phone']) ? $_POST['phone'] : null;

    if ($enteredOtp == $storedOtp && $enteredPhone == $storedPhone) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP or phone number']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>