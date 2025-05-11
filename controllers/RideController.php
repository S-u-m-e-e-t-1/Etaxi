<?php
require_once __DIR__ . '/../models/Ride.php';
require_once __DIR__ . '/../includes/database.php';

class RideController {
    private $rideModel;

    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->rideModel = new Ride($db);
    }

    public function bookRide() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $customer_id = $_POST['customer_id'];
            $pickup_location = $_POST['pickup_location'];
            $dropoff_location = $_POST['dropoff_location'];
            // Get base inputs for fare calculation
            $distance = $_POST['distance'];
            $time = $_POST['time'];
            
            // Define fixed rates since they are not in POST data
            $per_km_rate = 10;
            $per_minute_rate = 2;
            
            // Apply dynamic surcharge based on distance:
            // Higher the distance, lower the surcharge
            if ($distance <= 5) {
                $surcharges = 5;
            } elseif ($distance <= 10) {
                $surcharges = 3;
            } else {
                $surcharges = 2;
            }
            
            // Total Fare calculation using the formula:
            // Total Fare = (Distance × Per-KM Rate) + (Time × Per-Minute Rate) + Surcharges
            $total_fare = ($distance * $per_km_rate) + ($time * $per_minute_rate) + $surcharges;

            $result = $this->rideModel->bookRide($customer_id, $pickup_location, $dropoff_location, $total_fare, $distance, $per_km_rate);

            if ($result['success']) {
                header("Location: ../views/customer/book-ride.php?success=1&ride_id=" . $result['ride_id'] . "&fare=" . $total_fare);
            } else {
                header("Location: ../views/customer/book-ride.php?error=" . $result['error']);
            }
        }
    }

    public function getDriverRides($driver_id, $status) {
        return $this->rideModel->getDriverRides($driver_id, $status);
    }

    public function completeRide($ride_id, $driver_id, $otp) {
        return $this->rideModel->completeRide($ride_id, $driver_id, $otp);
    }

    public function cancelRide($ride_id) {
        return $this->rideModel->cancelRide($ride_id);
    }

    public function markRideAsPaid($ride_id) {
        return $this->rideModel->markRideAsPaid($ride_id);
    }

    public function getRideDetails($rideId) {
        return $this->rideModel->getRideDetails($rideId);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new RideController();
    
    // Check if action is set for API endpoints
    if (isset($_POST['action'])) {
        header('Content-Type: application/json');
        switch ($_POST['action']) {
            case 'completeRide':
                echo json_encode($controller->completeRide(
                    $_POST['ride_id'],
                    $_POST['driver_id'],
                    $_POST['otp']
                ));
                break;
            case 'cancelRide':
                echo json_encode($controller->cancelRide($_POST['ride_id']));
                break;
            case 'markRideAsPaid':
                echo json_encode($controller->markRideAsPaid($_POST['ride_id']));
                break;
            default:
                echo json_encode(["success" => false, "error" => "Invalid action"]);
        }
        exit;
    }
    
    // Handle regular form submission for booking
    $controller->bookRide();
}
?>