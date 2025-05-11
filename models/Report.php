<?php
class Statistics {
    private $db;

    public function __construct($dbConnection) {
        $this->db = $dbConnection;
    }

    public function getTotalDrivers() {
        $sql = "SELECT COUNT(*) as total FROM drivers";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalPayments() {
        $sql = "SELECT SUM(amount) as total FROM payments";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalKilometers() {
        $sql = "SELECT SUM(distance) as total FROM rides";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getTotalCustomers() {
        $sql = "SELECT COUNT(*) as total FROM customers";
        $result = $this->db->query($sql);
        $row = $result->fetch_assoc();
        return $row['total'];
    }

    public function getMonthlyRides($year, $month) {
        $sql = "SELECT COUNT(*) as total_rides, SUM(distance) as total_distance 
                FROM rides 
                WHERE MONTH(request_time) = ? AND YEAR(request_time) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getMonthlyEarnings($year, $month) {
        $sql = "SELECT COUNT(*) as total_payments, SUM(amount) as total_amount 
                FROM payments 
                WHERE MONTH(payment_date) = ? AND YEAR(payment_date) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getMonthlyCustomers($year, $month) {
        $sql = "SELECT COUNT(DISTINCT customer_id) as total_customers 
                FROM rides 
                WHERE MONTH(request_time) = ? AND YEAR(request_time) = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ii", $month, $year);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getMonthlyStats($year, $month) {
        $rides = $this->getMonthlyRides($year, $month);
        $earnings = $this->getMonthlyEarnings($year, $month);
        $customers = $this->getMonthlyCustomers($year, $month);

        return [
            'rides' => $rides['total_rides'] ?? 0,
            'distance' => $rides['total_distance'] ?? 0,
            'payments' => $earnings['total_payments'] ?? 0,
            'amount' => $earnings['total_amount'] ?? 0,
            'customers' => $customers['total_customers'] ?? 0
        ];
    }

    public function getYearlyStats($year) {
        $sql = "SELECT 
                    MONTH(request_time) as month,
                    COUNT(*) as total_rides,
                    SUM(distance) as total_distance,
                    COUNT(DISTINCT customer_id) as total_customers
                FROM rides 
                WHERE YEAR(request_time) = ?
                GROUP BY MONTH(request_time)
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $rides = [];
        while ($row = $result->fetch_assoc()) {
            $rides[$row['month']] = $row;
        }

        $sql = "SELECT 
                    MONTH(payment_date) as month,
                    COUNT(*) as total_payments,
                    SUM(amount) as total_amount
                FROM payments 
                WHERE YEAR(payment_date) = ?
                GROUP BY MONTH(payment_date)
                ORDER BY month";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $year);
        $stmt->execute();
        $result = $stmt->get_result();
        $payments = [];
        while ($row = $result->fetch_assoc()) {
            $payments[$row['month']] = $row;
        }

        $yearlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $yearlyStats[$month] = [
                'rides' => $rides[$month]['total_rides'] ?? 0,
                'distance' => $rides[$month]['total_distance'] ?? 0,
                'customers' => $rides[$month]['total_customers'] ?? 0,
                'payments' => $payments[$month]['total_payments'] ?? 0,
                'amount' => $payments[$month]['total_amount'] ?? 0
            ];
        }

        return $yearlyStats;
    }
}
?>