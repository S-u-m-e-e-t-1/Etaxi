<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

// Get admin session data
$adminData = $_SESSION['admin'];

$adminImage = '../../assets/images/' . (!empty($adminData['profile_image']) ? $adminData['profile_image'] : 'default.jpg'); // Default image if not set

// Admin panel options
$options = [
    ['name' => 'Home', 'link' => 'home.php', 'subItems' => []],
    ['name' => 'Manage Drivers', 'link' => 'manage-drivers.php', 'subItems' => []],
    ['name' => 'Manage Customers', 'link' => 'manage-customers.php', 'subItems' => []],
    ['name' => 'Ride Management', 'link' => 'manage-rides.php', 'subItems' => []],
    ['name' => 'Payments & Earnings', 'link' => 'manage-payments.php', 'subItems' => []],
    ['name' => 'Promo Codes', 'link' => 'manage-promocodes.php', 'subItems' => []],
    ['name' => 'Reports & Analytics', 'link' => 'report.php', 'subItems' => []],
    
    ['name' => 'Manage Blogs', 'link' => 'manage-blogs.php', 'subItems' => []],
];

// Include the dashboard layout
include '../dashboard_layout.php';

?>
