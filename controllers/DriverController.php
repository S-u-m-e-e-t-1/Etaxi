<?php
session_start(); // Add this at the top of the file
if (!isset($_SESSION['driver'])) {
    header('Location: ../../login.php');
    exit;
}


include_once __DIR__ . '/../includes/database.php';
include_once __DIR__ . '/../models/Ride.php';
include_once __DIR__ . '/../models/Payment.php';
include_once __DIR__ . '/../models/Messages.php';
include_once __DIR__ . '/../models/Notification.php';
include_once __DIR__ . '/../models/Driver.php';

class DriverController {
    private $db;
    private $ride;
    private $payment;
    private $messages;
    private $notification;
    private $driver;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->ride = new Ride($this->db);
        $this->payment = new Payment($this->db);
        $this->messages = new Messages($this->db);
        $this->notification = new Notification($this->db);
        $this->driver = new Driver($this->db);
    }

    public function getDashboardData($driver_id) {
        $totalRides = $this->ride->getTotalRidesByDriver($driver_id);
        $totalEarnings = $this->payment->getTotalEarningsByDriver($driver_id);
        $pendingRides = $this->ride->getAllPendingRides();

        return [
            "totalRides" => $totalRides['totalRides'],
            "totalEarnings" => $totalEarnings['totalEarnings'],
            "pendingRides" => $pendingRides['pendingRides']
        ];
    }

    public function acceptRide($ride_id, $driver_id) {
        // Check driver availability first
        $availability = $this->driver->getDriverAvailabilityStatus($driver_id);
        
        if (!$availability) {
            return [
                "success" => false,
                "error" => "Driver is currently unavailable for rides"
            ];
        }

        // If available, update availability status to unavailable
        $this->driver->updateAvailabilityStatus($driver_id, 'unavailable');

        // Then proceed with accepting the ride
        return $this->ride->acceptRide($ride_id, $driver_id);
    }

    public function getDriverAvailability($driver_id) {
        return $this->driver->getDriverAvailabilityStatus($driver_id);
    }

    public function getMessages($driver_email) {
        $sentMessages = $this->messages->getMessagesBySender($driver_email);
        $receivedMessages = $this->messages->getMessagesByReceiver($driver_email);

        return [
            "sentMessages" => $sentMessages,
            "receivedMessages" => $receivedMessages
        ];
    }

    public function sendMessage($data) {
        $senderEmail = $data['senderEmail'];
        $receiverEmail = $data['receiverEmail'];
        $content = $data['content'];
        
        return $this->messages->sendMessage($senderEmail, $receiverEmail, $content);
    }

    public function updateMessageStatus($messageId) {
        $result = $this->messages->updateMessageStatus($messageId, 'read');
        
        if ($result['success']) {
            return [
                'success' => true,
                'message' => 'Message marked as read successfully'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to update message status: ' . ($result['error'] ?? 'Unknown error')
            ];
        }
    }

    public function getDriverNotifications() {
        return $this->notification->getAllNotifications();
    }

    public function displayEditProfile() {
        if (!isset($_SESSION['driver'])) {
            header('Location: ../../login.php');
            exit;
        }

        $driver_id = $_SESSION['driver']['id'];
        $result = $this->driver->getDriverProfile($driver_id);
        
        if ($result['success']) {
            $profile = $result['profile'];
            include '../../views/driver/edit_profile_content.php';
        } else {
            $_SESSION['error'] = "Failed to load profile data";
            header('Location: dashboard.php');
            exit;
        }
    }

    public function handleProfileUpdate() {
        if (!isset($_SESSION['driver'])) {
            error_log("Driver not authenticated");
            echo json_encode(["success" => false, "error" => "Not authenticated"]);
            exit;
        }

        $driver_id = $_SESSION['driver']['id'];
        error_log("Updating profile for driver ID: $driver_id");

        try {
            $result = $this->driver->updateProfile($driver_id, $_POST, $_FILES);
            header('Content-Type: application/json');
            echo json_encode($result);
        } catch (Exception $e) {
            error_log("Error in handleProfileUpdate: " . $e->getMessage());
            echo json_encode(["success" => false, "error" => "An unexpected error occurred"]);
        }
        exit;
    }

    public function handleDriverProfileUpdate() {

        if (!isset($_SESSION['driver'])) {
            error_log("Driver not authenticated. Session data: " . print_r($_SESSION, true));
            echo json_encode(["success" => false, "error" => "Not authenticated"]);
            exit;
        }

        $driver_id = $_SESSION['driver']['id'];
        $result = $this->driver->updateProfile($driver_id, $_POST, $_FILES);

        echo json_encode($result);
        exit;
    }

    public function handleVehicleProfileUpdate() {
        if (!isset($_SESSION['driver'])) {
            echo json_encode(["success" => false, "error" => "Not authenticated"]);
            exit;
        }

        $driver_id = $_SESSION['driver']['id'];
        $result = $this->driver->updateVehicle($driver_id, $_POST, $_FILES);

        echo json_encode($result);
        exit;
    }

    public function getDriverProfile($driver_id) {
        return $this->driver->getDriverProfile($driver_id);
    }

    public function getDriverEarnings($driver_id) {
        return $this->payment->getDriverEarnings($driver_id);
    }

    public function getEditProfileData() {
        if (!isset($_SESSION['driver'])) {
            header('Location: ../../login.php');
            exit;
        }

        $driver_id = $_SESSION['driver']['id'];
        $result = $this->driver->getDriverProfile($driver_id);

        if (!$result['success']) {
            $_SESSION['error'] = "Failed to load profile data";
            header('Location: dashboard.php');
            exit;
        }

        return $result['profile'];
    }

    public function getHomePageData($driver_id) {
        $dashboardData = $this->getDashboardData($driver_id);
        $availability = $this->getDriverAvailability($driver_id);

        return [
            'dashboardData' => $dashboardData,
            'availability' => $availability
        ];
    }

    public function handleAcceptRide($rideId, $driverId) {
        $rideModel = new Ride($this->db);
        $result = $rideModel->acceptRide($rideId, $driverId);

        if ($result['success']) {
            // Redirect to "My Rides" page on success
            header('Location: ../../views/driver/my-rides.php');
            exit;
        } else {
            // Store the error in the session and redirect back to the home page
            $_SESSION['error'] = $result['error'];
            header('Location: home.php');
            exit;
        }
    }
}

// Add this to handle requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $controller = new DriverController();

    switch ($action) {
        case 'acceptRide':
            $rideId = $_POST['ride_id'] ?? null;
            $driverId = $_POST['driver_id'] ?? null;
            if ($rideId && $driverId) {
                $controller->handleAcceptRide($rideId, $driverId);
            } else {
                $_SESSION['error'] = 'Missing ride_id or driver_id';
                header('Location: home.php');
                exit;
            }
            break;

        default:
            $_SESSION['error'] = 'Invalid action';
            header('Location: home.php');
            exit;
    }
}

$controller = new DriverController();
$driver_id = $_SESSION['driver']['id'];

$result = $controller->getDriverEarnings($driver_id);
$profile = $controller->getEditProfileData();
if (!$result['success']) {
    $_SESSION['error'] = "Failed to load earnings data";
    header('Location: dashboard.php');
    exit;
}

$earnings = $result['earnings'];
?>