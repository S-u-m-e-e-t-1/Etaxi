<?php
class Payment {
    private $conn;
    private $table_name = "payments";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllPayments() {
        $sql = "SELECT p.id, p.ride_id, p.amount, p.payment_method, p.payment_status, p.payment_date, 
                       c.name as customer_name, d.name as driver_name
                FROM " . $this->table_name . " p
                JOIN customers c ON p.customer_id = c.id
                JOIN rides r ON p.ride_id = r.id
                JOIN drivers d ON r.driver_id = d.id";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "payments" => $payments];
    }

    public function getTotalEarningsByDriver($driver_id) {
        $sql = "SELECT SUM(amount) as totalEarnings FROM " . $this->table_name . " WHERE driver_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $driver_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $totalEarnings = $result->fetch_assoc()['totalEarnings'];

        return ["success" => true, "totalEarnings" => $totalEarnings];
    }

    public function getTotalPaymentsByCustomer($customer_id) {
        $sql = "SELECT SUM(amount) as totalPayments FROM " . $this->table_name . " WHERE customer_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return 0;
        }
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['totalPayments'] ?? 0;
    }

    public function savePayment($paymentData) {
        $sql = "INSERT INTO " . $this->table_name . " 
                (ride_id, driver_id, customer_id, amount, payment_id, payment_method, payment_status, payment_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Prepare failed: " . $this->conn->error];
        }

        $stmt->bind_param("iiidssss", 
            $paymentData['ride_id'],
            $paymentData['driver_id'],
            $paymentData['customer_id'],
            $paymentData['amount'],
            $paymentData['payment_id'],
            $paymentData['payment_method'],
            $paymentData['payment_status'],
            $paymentData['payment_date']
        );

        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => "Execute failed: " . $stmt->error];
        }
    }

    public function getAllPaymentsByCustomer($customer_id) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE customer_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = $result->fetch_all(MYSQLI_ASSOC);
        return ["success" => true, "payments" => $payments];
    }

    public function getPaymentByRideId($rideId) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE ride_id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $rideId);
        $stmt->execute();
        $result = $stmt->get_result();
        $payment = $result->fetch_assoc();
        
        if (!$payment) {
            return ["success" => false, "error" => "Payment not found"];
        }
        
        return ["success" => true, "payment" => $payment];
    }

    public function updateInvoicePath($paymentId, $invoicePath) {
        $sql = "UPDATE " . $this->table_name . " SET invoice = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("si", $invoicePath, $paymentId);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function getDriverEarnings($driver_id) {
        try {
            $sql = "SELECT p.id, p.ride_id, p.driver_id, p.customer_id, p.payment_id, 
                           p.amount, p.payment_method, p.payment_status, p.payment_date, p.invoice 
                    FROM payments p 
                    WHERE p.driver_id = ?
                    ORDER BY p.payment_date DESC";
                    
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return ["success" => false, "error" => "Database error"];
            }
            
            $stmt->bind_param("i", $driver_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $earnings = $result->fetch_all(MYSQLI_ASSOC);
            
            return [
                "success" => true,
                "earnings" => $earnings
            ];
            
        } catch (Exception $e) {
            error_log("Error fetching driver earnings: " . $e->getMessage());
            return [
                "success" => false,
                "error" => "Failed to fetch earnings"
            ];
        }
    }
}
?>