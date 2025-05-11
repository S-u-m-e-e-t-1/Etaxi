<?php
class Database {
    private $host = "localhost";
    private $db_name = "etaxi";
    private $username = "root";
    private $password = "";
    private $conn;

    public function getConnection() {
        if ($this->conn !== null) {
            return $this->conn; // Return existing connection if already established
        }

        try {
            $this->conn = new mysqli($this->host, $this->username, $this->password, $this->db_name);

            // Check for connection errors
            if ($this->conn->connect_error) {
                throw new Exception("Connection failed: " . $this->conn->connect_error);
            }
        } catch (Exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            return null; // Return null on failure
        }

        return $this->conn;
    }
}
?>
