<?php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/PromoCodes.php';
require_once __DIR__ . '/../includes/database.php';

class PaymentController {
    private $paymentModel;
    private $promoCodeModel;
    
    public function __construct() {
        $database = new Database();
        $db = $database->getConnection();
        $this->paymentModel = new Payment($db);
        $this->promoCodeModel = new PromoCodes($db);
    }
    
    public function savePayment($paymentData) {
        if (!$paymentData) {
            return ['success' => false, 'error' => 'No payment data provided'];
        }
        
        $result = $this->paymentModel->savePayment($paymentData);
        if ($result) {
            return ['success' => true, 'message' => 'Payment saved successfully'];
        } else {
            return ['success' => false, 'error' => 'Failed to save payment'];
        }
    }
    
    public function applyPromoCode($code, $amount) {
        try {
            if (empty($code) || empty($amount)) {
                return ['success' => false, 'error' => 'Invalid promo code or amount'];
            }

            $result = $this->promoCodeModel->validatePromoCode($code);
            
            if ($result['success']) {
                $discountPercentage = $result['promoCode']['discount_percentage'];
                $discount = ($amount * $discountPercentage) / 100;
                $finalAmount = $amount - $discount;
                
                return [
                    'success' => true,
                    'originalAmount' => $amount,
                    'discount' => $discount,
                    'finalAmount' => max(0, $finalAmount), // Ensure amount doesn't go below 0
                    'promoCode' => $result['promoCode']
                ];
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error in applyPromoCode: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error applying promo code'];
        }
    }
    
    // Add other payment-related methods as needed
}
?>
