<?php
class Messages {
    private $conn;
    private $table_name = "messages";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getMessagesBySender($senderEmail) {
        $query = "SELECT id, sender, receiver, content, created_at, status FROM " . $this->table_name . " WHERE sender = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $senderEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        return $messages;
    }

    public function getMessagesByReceiver($receiverEmail) {
        $query = "SELECT id, sender, receiver, content, created_at, status FROM " . $this->table_name . " WHERE receiver = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $receiverEmail);
        $stmt->execute();
        $result = $stmt->get_result();
        $messages = $result->fetch_all(MYSQLI_ASSOC);
        return $messages;
    }

    public function sendMessage($senderEmail, $receiverEmail, $content) {
        $query = "INSERT INTO " . $this->table_name . " (sender, receiver, content, created_at, status) VALUES (?, ?, ?, NOW(), 'delivered')";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $senderEmail, $receiverEmail, $content);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }

    public function updateMessageStatus($messageId, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $messageId);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }
}
?>