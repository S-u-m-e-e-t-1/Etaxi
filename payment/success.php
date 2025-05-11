<?php
require_once '../libs/razorpay/Razorpay.php';
use Razorpay\Api\Api;
require_once __DIR__ . '/../includes/database.php';
require_once '../controllers/RideController.php';
require_once '../controllers/PaymentController.php';

$api = new Api("rzp_test_Tcoz7vjPf8FW4k","cXXKPU05Pzh6js0wtFQJUKSb");
if (!isset($_GET['payment_id'])) {
    echo "Payment failed or canceled.";
    exit;
}

$payment_id = $_GET['payment_id'];
$ride_id = $_GET['ride_id'] ?? null;
$ride_fare = $_GET['ride_fare'] ?? null;
$rideController = new RideController();
$paymentController = new PaymentController();

try {
    // Fetch the payment details
    $payment = $api->payment->fetch($payment_id);

    if ($payment['status'] == 'captured') {
        // Example: Fetching ride details for a specific ride ID
        $rideDetails = $rideController->getRideDetails($ride_id);

        if ($rideDetails['success']) {
            $ride = $rideDetails['ride'];
            $paymentData = [
                'ride_id' => $ride['id'],
                'driver_id' => $ride['driver_id'],
                'customer_id' => $ride['customer_id'],
                'payment_id' => $payment['id'],
                'amount' => $ride['fare'],
                'payment_method' => 'Razorpay',
                'payment_status' => $payment['status'],
                'payment_date' => date('Y-m-d H:i:s')
            ];

            // Save payment details using the controller
            $saveResult = $paymentController->savePayment($paymentData);
            if ($saveResult['success']) {
                // Display success message
                echo "<h1>Payment Successful!</h1>";
                echo "<p>Payment ID: " . $payment['id'] . "</p>";
                echo "<p>Amount: " . ($payment['amount'] / 100) . " INR</p>";
                echo "<p>Status: " . $payment['status'] . "</p>";
                
                // Call to mark the ride as paid
                $markPaidResult = $rideController->markRideAsPaid($ride_id);
                if ($markPaidResult['success']) {
                    echo "<p>Ride marked as paid successfully.</p>";
                } else {
                    echo "<p>Error marking ride as paid: " . $markPaidResult['error'] . "</p>";
                }
            } else {
                echo "Error saving payment: " . $saveResult['error'];
            }
        } else {
            echo "Error fetching ride details: " . $rideDetails['error'];
        }
    } else {
        echo "<h1>Payment Failed or Pending!</h1>";
        echo "<p>Status: " . $payment['status'] . "</p>";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

?>
<head>
    <style>
        h1 { color: green; }
        p { font-size: 16px; }
    </style>
</head>
<script>
    setTimeout(function() {
        window.location.href = '/ETaxi/views/customer/my-rides.php';
    }, 5000); // Redirect after 5 seconds
</script>
