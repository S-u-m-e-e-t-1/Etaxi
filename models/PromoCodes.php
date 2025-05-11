<?php
class PromoCodes {
    private $conn;
    private $table_name = "promo_codes";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllPromoCodes() {
        try {
            $sql = "SELECT id, code, discount_percentage, valid_from, valid_to, created_at FROM " . $this->table_name;
            $stmt = $this->conn->prepare($sql);
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return ["success" => false, "error" => "Database error"];
            }
            $stmt->execute();
            $result = $stmt->get_result();
            $promoCodes = $result->fetch_all(MYSQLI_ASSOC);

            return ["success" => true, "promoCodes" => $promoCodes];
        } catch (Exception $e) {
            error_log("Error in getAllPromoCodes: " . $e->getMessage());
            return ["success" => false, "error" => "Database error"];
        }
    }
    
    public function validatePromoCode($code) {
        try {
            if (empty($code)) {
                return ["success" => false, "error" => "Promo code cannot be empty"];
            }

            $sql = "SELECT * FROM " . $this->table_name . " WHERE code = ? AND valid_from <= CURRENT_TIMESTAMP AND valid_to >= CURRENT_TIMESTAMP LIMIT 1";
            $stmt = $this->conn->prepare($sql);
            
            if (!$stmt) {
                error_log("Prepare failed: " . $this->conn->error);
                return ["success" => false, "error" => "Database error"];
            }
            
            $stmt->bind_param("s", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            $promoCode = $result->fetch_assoc();
            
            if ($promoCode) {
                return [
                    "success" => true,
                    "promoCode" => $promoCode
                ];
            }
            
            return ["success" => false, "error" => "Invalid or expired promo code"];
        } catch (Exception $e) {
            error_log("Error in validatePromoCode: " . $e->getMessage());
            return ["success" => false, "error" => "Error validating promo code"];
        }
    }
}
?>