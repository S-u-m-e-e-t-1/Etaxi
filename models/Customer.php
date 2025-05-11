<?php
class Customer {
    private $conn;
    private $table_name = "customers";

    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function register($data) {
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $password = $data['password'];
        $created_at = $data['created_at'];

        // Check if email or phone already exists
        $check_sql = "SELECT id FROM " . $this->table_name . " WHERE email = ? OR phone = ?";
        $stmt = $this->conn->prepare($check_sql);
        if (!$stmt) {
            return ["success" => false, "error" => "Database error: " . $this->conn->error];
        }
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            return ["success" => false, "error" => "Email or phone already exists!"];
        }

        // Insert new customer
        $sql = "INSERT INTO " . $this->table_name . " (name, email, phone, password, created_at) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            return ["success" => false, "error" => "Database error: " . $this->conn->error];
        }
        $stmt->bind_param("sssss", $name, $email, $phone, $password, $created_at);

        if ($stmt->execute()) {
            return ["success" => true, "message" => "Registration successful"];
        } else {
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function login($email, $password) {
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // if ($user && password_verify($password, $user['password'])) {
        if ($password===$user['password']) {
            return ["success" => true, "id" => $user['id'], "user" => $user, "role" => "customer"];
        } else {
            return ["success" => false, "error" => "Invalid email or password"];
        }
    }

    public function loginWithOtp($email, $otp) {
        $sql = "SELECT * FROM customers WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && $_SESSION['reset_otp'] == $otp && $_SESSION['reset_email'] == $email) {
            return ["success" => true, "id" => $user['id'], "customer" => $user, "role" => "customer"];
        } else {
            return ["success" => false, "error" => "Invalid OTP"];
        }
    }

    public function getAllCustomers() {
        $sql = "SELECT id, name, email, phone, profile_image, created_at FROM " . $this->table_name;
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $customers = $result->fetch_all(MYSQLI_ASSOC);

        return ["success" => true, "customers" => $customers];
    }

    public function deleteCustomer($customerId) {
        $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("i", $customerId);
        if ($stmt->execute()) {
            return ["success" => true];
        } else {
            error_log("Execute failed: " . $stmt->error);
            return ["success" => false, "error" => "Error: " . $stmt->error];
        }
    }

    public function getCustomerByEmail($email) {
        $sql = "SELECT * FROM " . $this->table_name . " WHERE email = ?";
        $stmt = $this->conn->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->conn->error);
            return ["success" => false, "error" => "Database error"];
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $customer = $result->fetch_assoc();

        if ($customer) {
            return $customer;
        } else {
            return null;
        }
    }

    public function updateProfile($customerId, $name, $email, $phone, $profileImage) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = ?, email = ?, phone = ?, profile_image = ? 
                  WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssi", $name, $email, $phone, $profileImage, $customerId);
        if($stmt->execute()){
            return ["success" => true];
        } else {
            return ["success" => false, "error" => $stmt->error];
        }
    }
}
?>