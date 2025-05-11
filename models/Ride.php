<?php
require_once __DIR__ . '/Driver.php';

class Ride {
    private $conn;
    private $table_name = "rides";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllRides() {
        $sql = "SELECT * FROM " . $this->table_name . " ORDER BY id DESC";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $rides = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "rides" => $rides];
    }

    public function getRideDetails($ride_id) {
        $sql = "SELECT r.*, 
                       c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone, c.profile_image AS customer_profile,
                       d.name AS driver_name, d.email AS driver_email, d.phone AS driver_phone, d.profile_image AS driver_profile
                FROM " . $this->table_name . " r
                JOIN customers c ON r.customer_id = c.id
                JOIN drivers d ON r.driver_id = d.id
                WHERE r.id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $ride_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $ride = $result->fetch_assoc();

        if (!$ride) {
            return ["success" => false, "error" => "Ride not found"];
        }

        // Fetch reviews for this ride
        $reviews = $this->getRideReviews($ride_id);

        return ["success" => true, "ride" => $ride, "reviews" => $reviews];
    }

    private function getRideReviews($ride_id) {
        $sql = "SELECT id, ride_id, customer_id, driver_id, rating, review, created_at FROM reviews WHERE ride_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return [];
        }
        $stmt->bind_param("i", $ride_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function bookRide($customer_id, $pickup_location, $dropoff_location, $fare, $distance, $rate) {
        $sql = "INSERT INTO " . $this->table_name . " 
                (customer_id, pickup_location, dropoff_location, ride_status, fare, request_time, distance, rate) 
                VALUES (?, ?, ?, 'pending', ?, NOW(), ?, ?)";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("issddi", $customer_id, $pickup_location, $dropoff_location, $fare, $distance, $rate);

        if ($stmt->execute()) {
            return ["success" => true, "ride_id" => $this->conn->insert_id];
        } else {
            return ["success" => false, "error" => "Failed to book ride"];
        }
    }

    public function getRidesByCustomer($customerId) {
        $query = "SELECT r.id, r.driver_id, r.pickup_location, r.dropoff_location, r.ride_status, r.fare, r.request_time, r.distance, r.rate, 
                         d.name AS driver_name
                  FROM " . $this->table_name . " r
                  LEFT JOIN drivers d ON r.driver_id = d.id
                  WHERE r.customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customerId);
        $stmt->execute();
        $result = $stmt->get_result();
        $rides = $result->fetch_all(MYSQLI_ASSOC);
        return $rides;
    }

    public function getLatestRidesByCustomer($customerId, $limit = 5) {
        $query = "SELECT r.id, r.driver_id, r.pickup_location, r.dropoff_location, r.ride_status, r.fare, r.request_time, 
                         d.name AS driver_name
                  FROM " . $this->table_name . " r
                  LEFT JOIN drivers d ON r.driver_id = d.id
                  WHERE r.customer_id = ?
                  ORDER BY r.request_time DESC
                  LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $customerId, $limit);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function cancelRide($rideId) {
        $query = "UPDATE " . $this->table_name . " SET ride_status = 'cancelled' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $rideId);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function getTotalRidesByDriver($driver_id) {
        $sql = "SELECT COUNT(*) as totalRides FROM " . $this->table_name . " WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalRides = $result->fetch_assoc()['totalRides'];

        return ["success" => true, "totalRides" => $totalRides];
    }

    public function getPendingRidesByDriver($driver_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE driver_id = ? AND ride_status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pendingRides = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "pendingRides" => $pendingRides];
    }

    public function getAllPendingRides() {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE ride_status = 'pending'";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $pendingRides = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "pendingRides" => $pendingRides];
    }

    public function acceptRide($ride_id, $driver_id) {
        try {
            // Check if the driver is approved
            $driverCheckSql = "SELECT status FROM drivers WHERE id = ?";
            $driverCheckStmt = $this->conn->prepare($driverCheckSql);
            if (!$driverCheckStmt) {
                throw new Exception("Prepare failed for driver status check: " . $this->conn->error);
            }
            $driverCheckStmt->bind_param("i", $driver_id);
            $driverCheckStmt->execute();
            $driverResult = $driverCheckStmt->get_result();
            $driver = $driverResult->fetch_assoc();

            if (!$driver || $driver['status'] !== 'approved') {
                return ["success" => false, "error" => "Driver is not approved"];
            }

            // Check if the driver is available
            $availabilityCheckSql = "SELECT is_available FROM driver_availability WHERE driver_id = ?";
            $availabilityCheckStmt = $this->conn->prepare($availabilityCheckSql);
            if (!$availabilityCheckStmt) {
                throw new Exception("Prepare failed for driver availability check: " . $this->conn->error);
            }
            $availabilityCheckStmt->bind_param("i", $driver_id);
            $availabilityCheckStmt->execute();
            $availabilityResult = $availabilityCheckStmt->get_result();
            $availability = $availabilityResult->fetch_assoc();

            if (!$availability || $availability['is_available'] != 1) {
                return ["success" => false, "error" => "Driver is not available"];
            }

            // Update ride status to 'accepted' and assign driver
            $sql = "UPDATE " . $this->table_name . " SET ride_status = 'accepted', driver_id = ? WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }
            $stmt->bind_param("ii", $driver_id, $ride_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update ride status: " . $stmt->error);
            }

            // Generate OTP and update the ride
            $otp = rand(100000, 999999);
            $updateOtpSql = "UPDATE " . $this->table_name . " SET OTP = ? WHERE id = ?";
            $updateOtpStmt = $this->conn->prepare($updateOtpSql);
            if (!$updateOtpStmt) {
                throw new Exception("Prepare failed for OTP update: " . $this->conn->error);
            }
            $updateOtpStmt->bind_param("ii", $otp, $ride_id);
            if (!$updateOtpStmt->execute()) {
                throw new Exception("Failed to update OTP: " . $updateOtpStmt->error);
            }

            // Update driver availability
            $driverModel = new Driver($this->conn);
            $availabilityResult = $driverModel->updateDriverAvailability($driver_id, 0);
            if (!$availabilityResult['success']) {
                throw new Exception("Failed to update driver availability: " . $availabilityResult['error']);
            }

            // Fetch ride details and notify the customer
            $rideDetails = $this->getRideDetails($ride_id);
            if ($rideDetails['success']) {
                $this->sendMessageToCustomer($rideDetails['ride'], $otp);
            }

            return ["success" => true];
        } catch (Exception $e) {
            error_log("Accept ride failed: " . $e->getMessage());
            return ["success" => false, "error" => $e->getMessage()];
        }
    }

    private function sendMessageToCustomer($ride, $otp) {
        $customerPhone = $ride['customer_phone'];
        $driverName = $ride['driver_name'];
        $pickupLocation = $ride['pickup_location'];
        $dropoffLocation = $ride['dropoff_location'];
        $message = "Your ride has been accepted by $driverName. Pickup Location: $pickupLocation, Dropoff Location: $dropoffLocation. Your OTP is $otp.";

        // Prepare the data for the message
        $params = [
            'token' => 'gri3ic1y633mzmfu',
            'to' => $customerPhone,
            'body' => $message
        ];

        // Initialize cURL
        $ch = curl_init("https://api.ultramsg.com/instance109742/messages/chat");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params),
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/x-www-form-urlencoded"
            ],
        ]);

        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        // Check if the request was successful
        if ($httpCode === 200 && !$err) {
            $result = json_decode($response, true);
            if ($result && $result['success']) {
                return true;
            }
        }

        // Log the error but don't fail the ride acceptance
        error_log("Failed to send message to customer: " . ($err ?: $response));
        return false;
    }

    public function getDriverRides($driver_id, $status) {
        $sql = "SELECT r.*, c.name as customer_name, c.phone as customer_phone 
                FROM " . $this->table_name . " r 
                JOIN customers c ON r.customer_id = c.id 
                WHERE r.driver_id = ? AND r.ride_status = ? 
                ORDER BY r.request_time DESC";
                
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        
        $stmt->bind_param("is", $driver_id, $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $rides = $result->fetch_all(MYSQLI_ASSOC);
        
        return ["success" => true, "rides" => $rides];
    }

    public function completeRide($ride_id, $driver_id, $otp) {
        // Validate inputs
        if (!is_numeric($ride_id) || !is_numeric($driver_id) || !is_numeric($otp)) {
            return ["success" => false, "error" => "Invalid input parameters"];
        }

        // Verify OTP
        $verify_sql = "SELECT OTP, ride_status FROM " . $this->table_name . " 
                       WHERE id = ? AND driver_id = ?";
        $stmt = $this->conn->prepare($verify_sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error: " . $this->conn->error];
        }
        
        $stmt->bind_param("ii", $ride_id, $driver_id);
        if (!$stmt->execute()) {
            error_log("Execute failed: " . $stmt->error);
            return ["success" => false, "error" => "Database error: " . $stmt->error];
        }
        
        $result = $stmt->get_result();
        $ride = $result->fetch_assoc();
        
        if (!$ride) {
            return ["success" => false, "error" => "Ride not found"];
        }

        if ($ride['ride_status'] !== 'paid') {
            return ["success" => false, "error" => "Ride is not in paid state"];
        }
        
        if ($ride['OTP'] != $otp) {
            return ["success" => false, "error" => "Invalid OTP"];
        }
        
        // Begin transaction
        $this->conn->begin_transaction();
        
        try {
            // Update ride status
            $update_sql = "UPDATE " . $this->table_name . " 
                          SET ride_status = 'completed', complete_time = NOW(), OTP = NULL 
                          WHERE id = ?";
            $stmt = $this->conn->prepare($update_sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare ride update statement: " . $this->conn->error);
            }
            $stmt->bind_param("i", $ride_id);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update ride status: " . $stmt->error);
            }

            // Update driver availability
            $driver = new Driver($this->conn);
            $driverUpdateResult = $driver->updateDriverAvailability($driver_id, 1);
            if (!$driverUpdateResult['success']) {
                throw new Exception("Failed to update driver availability: " . ($driverUpdateResult['error'] ?? 'Unknown error'));
            }

            $this->conn->commit();
            return ["success" => true];
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Complete ride failed: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return ["success" => false, "error" => "Failed to complete ride: " . $e->getMessage()];
        }
    }

    public function markRideAsPaid($ride_id) {
        $sql = "UPDATE " . $this->table_name . " SET ride_status = 'paid' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $ride_id);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

}
?>