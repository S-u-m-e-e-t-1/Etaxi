<?php

class Admin {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        if (!$this->conn) { // Debugging: Check if connection exists
            error_log("Database connection is null in Admin class.");
            return ["success" => false, "error" => "Database connection error"];
        }
        
        $sql = "SELECT * FROM admins WHERE email = ?";
        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && $password === $admin['password']) {
            return ["success" => true, "admin" => $admin, "role" => "admin"];
        } else {
            return ["success" => false, "error" => "Invalid email or password"];
        }
    }

    public function verifyPassword($adminPassword) {
        $admin = $_SESSION['admin']; // Assuming the entire admin data is stored in session

        if ($admin && $adminPassword === $admin['password']) {
            return true;
        } else {
            return false;
        }
    }

    public function getAdminById($id) {
        $sql = "SELECT id, name, email, phone, profile_image, created_at FROM admins WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function updateAdmin($id, $data) {
        $fields = [];
        $params = [];
        $types = "";

        foreach ($data as $key => $value) {
            $fields[] = "`$key` = ?";
            $params[] = $value;
            $types .= is_int($value) ? "i" : "s";
        }

        $sql = "UPDATE admins SET " . implode(", ", $fields) . " WHERE id = ?";
        $params[] = $id;
        $types .= "i";

        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "error" => $this->conn->error];
        }

        $stmt->bind_param($types, ...$params);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }
}
?>