<?php
class Review {
    private $conn;
    private $table_name = "reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function submitReview($data) {
        $query = "INSERT INTO " . $this->table_name . " (ride_id, customer_id, driver_id, rating, review, created_at) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bind_param('iiisss', $data['ride_id'], $data['customer_id'], $data['driver_id'], $data['rating'], $data['review'], $data['created_at']);

        if ($stmt->execute()) {
            return ['success' => true];
        } else {
            return ['success' => false, 'error' => $stmt->error];
        }
    }
}
?>