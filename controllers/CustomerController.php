<?php
require_once __DIR__ . '/../models/Customer.php';
require_once __DIR__ . '/../models/Messages.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Ride.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../includes/Database.php';

session_start();

$database = new Database();
$db = $database->getConnection();

// Instantiate models
$customerModel = new Customer($db);
$messages = new Messages($db);
$notification = new Notification($db);
$ride = new Ride($db);
$review = new Review($db);
$payment = new Payment($db);

// Handle form submission for updating profile
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'updateProfile') {
    // Get customer id from session
    $customerId = $_SESSION['customer']['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Default to existing profile image
    $profileImage = $_SESSION['customer']['profile_image'];

    // Process file upload if available
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/../uploads/users/profile/';
        $fileName = basename($_FILES['profile_image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $targetFilePath)) {
            $profileImage = $fileName;
        }
    }

    // Update customer data via Customer model
    $customerModel = new Customer($db);
    $result = $customerModel->updateProfile($customerId, $name, $email, $phone, $profileImage);
    if ($result['success']) {
        // Update session data with the new profile values
        $_SESSION['customer'] = array_merge($_SESSION['customer'], [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'profile_image' => $profileImage
        ]);
        $_SESSION['success_message'] = "Profile updated successfully.";
    } else {
        $_SESSION['error_message'] = $result['error'];
    }
    header("Location: ../views/customer/edit-profile.php");
    exit;
}

// ... existing JSON API handling code follows ...
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON data from request body
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if ($data === null) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }
    
    $action = $data['action'] ?? '';
    
    if ($action === 'sendMessage') {
        $senderEmail = $data['senderEmail'];
        $receiverEmail = $data['receiverEmail'];
        $content = $data['content'];
        $result = $messages->sendMessage($senderEmail, $receiverEmail, $content);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action === 'viewDetails') {
        $messageId = $data['messageId'];
        $result = $messages->updateMessageStatus($messageId, 'seen');
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action === 'cancelRide') {
        $rideId = $data['rideId'];
        $result = $ride->cancelRide($rideId);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } elseif ($action === 'submitReview') {
        $reviewData = [
            'ride_id' => $data['rideId'],
            'customer_id' => $_SESSION['customer']['id'],
            'driver_id' => $data['driverId'],
            'rating' => $data['rating'],
            'review' => $data['review'],
            'created_at' => date('Y-m-d H:i:s')
        ];
        $result = $review->submitReview($reviewData);
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['customer'])) {
        header('Location: ../../login.php');
        exit;
    }

    $customerData = $_SESSION['customer'];
    
    // Get total rides and payments for the customer
    $customerRides = $ride->getRidesByCustomer($customerData['id']);
    $totalRides = count($customerRides);
    
    // Get total payments
    $totalPayments = $payment->getTotalPaymentsByCustomer($customerData['id']);
    
    // Get latest 5 rides
    $latestRides = $ride->getLatestRidesByCustomer($customerData['id'], 5);
    
    // Get notifications for the current page
    if (basename($_SERVER['PHP_SELF']) === 'notifications.php') {
        $result = $notification->getAllNotifications();
        if ($result['success']) {
            $notifications = $result['notifications'];
        } else {
            $_SESSION['error_message'] = $result['error'];
            $notifications = [];
        }
    }

    $rides = $ride->getRidesByCustomer($customerData['id']);
    $sentMessages = $messages->getMessagesBySender($customerData['email']);
    $receivedMessages = $messages->getMessagesByReceiver($customerData['email']);
    $customerPayments = $payment->getAllPaymentsByCustomer($customerData['id']);

}
?>