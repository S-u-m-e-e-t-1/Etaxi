<?php
session_start();

// Check if customer is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: ../../login.php');
    exit;
}

// Get customer session data
$adminData = $_SESSION['customer'];
$adminImage = '../../uploads/users/profile/' . (!empty($adminData['profile_image']) ? $adminData['profile_image'] : 'default.jpg'); // Default image if not set

// Customer panel options
$options = [
    ['name' => 'Home', 'link' => 'home.php', 'subItems' => []],
    ['name' => 'Book a Ride', 'link' => 'book-ride.php', 'subItems' => []],
    ['name' => 'My Rides', 'link' => 'my-rides.php', 'subItems' => []],
    ['name' => 'Payments', 'link' => 'payments.php', 'subItems' => []],
    ['name' => 'Blogs', 'link' => 'blogs.php', 'subItems' => []],

];

// Include the dashboard layout
include '../dashboard_layout.php';
?>
