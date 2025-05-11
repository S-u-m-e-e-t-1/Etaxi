<?php
require_once '../libs/razorpay/Razorpay.php';
require_once '../controllers/PaymentController.php';
use Razorpay\Api\Api;
$ride_id = $_GET['ride_id'] ?? null;
$ride_fare = $_GET['ride_fare'] ?? null; // Fetching the ride fare from the query string

if (!$ride_id || !$ride_fare) {
    die('Ride ID and fare are required');
}

// Add after ride fare validation
$promoCode = $_GET['promo_code'] ?? null;
$finalAmount = $ride_fare;
$ride_fare_paise = $ride_fare * 100;
$promoMessage = '';

if ($promoCode) {
    $paymentController = new PaymentController();
    $promoResult = $paymentController->applyPromoCode($promoCode, $ride_fare);
    
    if ($promoResult['success']) {
        $finalAmount = $promoResult['finalAmount'];
        $ride_fare_paise = $finalAmount * 100;
        $promoMessage = "Promo code applied! Discount: ₹" . number_format($promoResult['discount'], 2);
    } else {
        $promoMessage = "Error: " . ($promoResult['error'] ?? 'Invalid promo code');
    }
}

// Initialize Razorpay API with your key and secret
$api = new Api("rzp_test_Tcoz7vjPf8FW4k","cXXKPU05Pzh6js0wtFQJUKSb");

// Create an order with the ride fare
$order = $api->order->create([
    'amount' => $ride_fare_paise, // Amount in paise
    'currency' => 'INR',  
    'payment_capture' => 1
]);

$order_id = $order->id;

?>

<!DOCTYPE html>
<html class="h-100">
<head>
    <title>Razorpay Payment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="h-100 d-flex align-items-center justify-content-center bg-light py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 col-lg-5">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h2 class="card-title text-center mb-4">Complete Your Payment</h2>
                        
                        <!-- Promo Code Section -->
                        <div class="mb-4">
                            <div class="input-group">
                                <input type="text" 
                                       id="promo-code" 
                                       class="form-control" 
                                       placeholder="Enter Promo Code">
                                <button id="apply-promo-btn" 
                                        class="btn btn-primary">
                                    Apply
                                </button>
                            </div>
                            <p id="promo-message" class="mt-2 small"></p>
                        </div>

                        <!-- Amount Details -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Original Amount:</span>
                                <span>₹<?= number_format($ride_fare, 2) ?></span>
                            </div>
                            <?php if ($promoCode && isset($promoResult['success']) && $promoResult['success']): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount:</span>
                                <span>-₹<?= number_format($promoResult['discount'], 2) ?></span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Final Amount:</span>
                                <span>₹<?= number_format($finalAmount, 2) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Button -->
                        <div class="text-center">
                            <button id="pay-button" class="btn btn-success btn-lg px-5">
                                Pay Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function applyPromoCode() {
            const promoCode = document.getElementById('promo-code').value;
            if (!promoCode) {
                showMessage('Please enter a promo code', 'text-danger');
                return;
            }
            window.location.href = window.location.pathname + 
                '?ride_id=<?= $ride_id ?>&ride_fare=<?= $ride_fare ?>&promo_code=' + encodeURIComponent(promoCode);
        }

        function showMessage(message, className = 'text-success') {
            const messageElement = document.getElementById('promo-message');
            messageElement.className = `mt-2 small ${className}`;
            messageElement.textContent = message;
        }

        var options = {
            "key": "rzp_test_Tcoz7vjPf8FW4k",
            "amount": "<?= $ride_fare_paise ?>",
            "currency": "INR",
            "name": "Your Company Name",
            "description": "Payment for your order",
            "order_id": "<?= $order_id ?>",
            "theme": { "color": "#198754" },
            "handler": function(response) {
                showMessage('Payment successful! Redirecting...');
                window.location.href = 'success.php?payment_id=' + response.razorpay_payment_id + 
                    '&ride_id=' + <?= $ride_id ?> + 
                    '&ride_fare=' + <?= $finalAmount ?>;
            },
            "modal": {
                "ondismiss": function() {
                    showMessage('Payment cancelled or failed. Please try again.', 'text-danger');
                }
            }
        };

        var rzp = new Razorpay(options);

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('apply-promo-btn').addEventListener('click', applyPromoCode);
            
            document.getElementById('pay-button').addEventListener('click', function(e) {
                rzp.open();
                e.preventDefault();
            });

            document.getElementById('pay-button').innerText = 'Pay ₹' + <?= $finalAmount ?> + ' Now';

            <?php if (!empty($promoMessage)): ?>
            showMessage(<?= json_encode($promoMessage) ?>, 
                       <?= isset($promoResult['success']) && $promoResult['success'] ? "'text-success'" : "'text-danger'" ?>);
            <?php endif; ?>
        });
    </script>
</body>
</html>
