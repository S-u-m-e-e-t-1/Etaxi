<?php
session_start();
header('Content-Type: application/json'); 

include_once __DIR__ . '/../includes/database.php';
include_once __DIR__ . '/../models/Customer.php';
include_once __DIR__ . '/../models/Driver.php';
include_once __DIR__ . '/../models/Admin.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['action'])) {
    echo json_encode(["success" => false, "error" => "Invalid request"]);
    exit;
}

$database = new Database();
$db = $database->getConnection();

$response = ["success" => false, "error" => "Invalid email or password"];

// Validate required fields
if (!isset($_POST['action']) && !isset($_GET['action'])) {
    echo json_encode(["success" => false, "error" => "Missing required fields"]);
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : $_GET['action'];

if ($action === 'register') {
    $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'phone' => trim($_POST['phone']),
        'otp' => trim($_POST['otp']),
        'password' => password_hash(trim($_POST['password']), PASSWORD_BCRYPT),
        'created_at' => date('Y-m-d H:i:s')
    ];

    // Verify OTP
    if (!isset($_SESSION['otp']) || $_SESSION['otp'] != $data['otp'] || $_SESSION['otp_phone'] != $data['phone']) {
        echo json_encode(["success" => false, "error" => "Invalid or unverified OTP"]);
        exit;
    }

    // Proceed with registration if OTP is verified
    $customer = new Customer($db);
    $response = $customer->register($data);
    echo json_encode($response);
    exit;
}

if ($action === 'register_driver') {
    $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'phone' => trim($_POST['phone']),
        'id_number' => trim($_POST['id_number']),
        'vehicle_type' => trim($_POST['vehicle_type']),
        'vehicle_model' => trim($_POST['vehicle_model']),
        'vehicle_number' => trim($_POST['vehicle_number']),
        'license_number' => trim($_POST['license_number']),
        'profile_image' => $_FILES['profile_image'],
        'id_image' => $_FILES['id_image'],
        'vehicle_image' => $_FILES['vehicle_image'],
        'license_image' => $_FILES['license_image']
    ];

    $driver = new Driver($db);
    $response = $driver->register($data);
    // echo json_encode($response);
    header("Location: ../login.php");
    exit;
}

$email = trim($_POST['email']);
$password = trim($_POST['password']);
$role = trim($_POST['role']);

// Process login
if ($action === 'login') {
    switch ($role) {
        case 'customer':
             $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);
    $customer = new Customer($db);
    $response = $customer->loginWithOtp($email, $otp);
    if ($response['success']) {
        $_SESSION['role'] = 'customer';
        $_SESSION['customer_id'] = $response['id'];
        $_SESSION['customer'] = $response['customer'];
    }
    break;
        case 'driver':
             $phone = trim($_POST['phone']);
            $otp = trim($_POST['otp']);
            $driver = new Driver($db);
            $response = $driver->loginWithOtp($phone, $otp);
            if ($response['success']) {
                $_SESSION['role'] = 'driver';
                $_SESSION['driver_id'] = $response['id'];
                $_SESSION['driver'] = $response['driver'];
    }
    break;
        case 'admin':
            $admin = new Admin($db);
            $response = $admin->login($email, $password);
            if ($response['success']){
                $_SESSION['role'] = 'admin';
                $_SESSION['admin'] = $response['admin'];
                break;
            } 
        default:
            $response = ["success" => false, "error" => "Invalid role"];
    }
}

if ($action === 'login_driver_otp') {
    $phone = trim($_POST['phone']);
    $otp = trim($_POST['otp']);
    $driver = new Driver($db);
    $response = $driver->loginWithOtp($phone, $otp);
    if ($response['success']) {
        $_SESSION['driver_id'] = $response['id'];
        $_SESSION['driver'] = $response['driver'];
    }
    echo json_encode($response);
    exit;
}

if ($action === 'login_customer_otp') {
    $email = trim($_POST['email']);
    $otp = trim($_POST['otp']);
    $customer = new Customer($db);
    $response = $customer->loginWithOtp($email, $otp);
    if ($response['success']) {
        $_SESSION['customer_id'] = $response['id'];
        $_SESSION['customer'] = $response['customer'];
    }
}

echo json_encode($response);
exit;
?>
