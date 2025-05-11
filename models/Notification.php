<?php
class Notification {
    private $conn;
    private $table_name = "notifications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllNotifications() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "notifications" => $notifications];
    }

    public function addNotification($created_by, $message) {
        $query = "INSERT INTO " . $this->table_name . " (created_by, message, created_at) 
                  VALUES (?, ?, NOW())";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("is", $created_by, $message);
        
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function deleteNotification($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function updateNotification($id, $message) {
        $query = "UPDATE " . $this->table_name . " SET message = ?, created_at = NOW() WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("si", $message, $id);
        
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }
}
?>