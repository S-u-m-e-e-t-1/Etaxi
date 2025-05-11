<?php

require_once '../../controllers/CustomerController.php';
// Check if customer is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: ../../login.php');
    exit;
}
$customerData = $_SESSION['customer'];
$customerRides = $ride->getRidesByCustomer($customerData['id']);
$totalRides = count($customerRides);
$totalPayments = $payment->getTotalPaymentsByCustomer($customerData['id']);
$latestRides = $ride->getLatestRidesByCustomer($customerData['id'], 5);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <!-- Dashboard Summary -->
    <div class="row">
        <div class="col-md-6">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Rides</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $totalRides ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Payments</div>
                <div class="card-body">
                    <h4 class="card-title">₹<?= number_format($totalPayments, 2) ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Rides -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Recent Rides</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ride ID</th>
                        <th>Driver</th>
                        <th>Pickup</th>
                        <th>Drop-off</th>
                        <th>Status</th>
                        <th>Fare</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($latestRides as $ride): ?>
                    <tr>
                        <td>#<?= $ride['id'] ?></td>
                        <td><?= $ride['driver_name'] ?? 'Not Assigned' ?></td>
                        <td><?= $ride['pickup_location'] ?></td>
                        <td><?= $ride['dropoff_location'] ?></td>
                        <td><span class="badge bg-<?= getRideStatusColor($ride['ride_status']) ?>"><?= ucfirst($ride['ride_status']) ?></span></td>
                        <td>$<?= number_format($ride['fare'], 2) ?></td>
                        <td><?= date('M d, Y H:i', strtotime($ride['request_time'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Current Location Map -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Your Current Location</h4>
        </div>
        <div class="card-body">
            <div id="map" style="height: 300px;"></div>
        </div>
    </div>
</div>

<?php
function getRideStatusColor($status) {
    switch ($status) {
        case 'completed':
            return 'success';
        case 'ongoing':
            return 'primary';
        case 'cancelled':
            return 'danger';
        case 'pending':
            return 'warning';
        default:
            return 'secondary';
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAhC0pX3qvfsOUFqvh0iztqgdA52kX-ZW4"></script>
<script>
// Initialize the map with user's current location
function initMap() {
    // Create a map centered on a default location
    const map = new google.maps.Map(document.getElementById('map'), {
        zoom: 15,
        center: { lat: 0, lng: 0 }
    });

    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                // Center map on user's location
                map.setCenter(pos);

                // Add marker for user's location
                new google.maps.Marker({
                    position: pos,
                    map: map,
                    title: 'Your Location'
                });
            },
            () => {
                // Handle location error
                console.error('Error: The Geolocation service failed.');
            }
        );
    } else {
        // Browser doesn't support geolocation
        console.error('Error: Your browser doesn\'t support geolocation.');
    }
}

// Initialize map when page loads
window.onload = initMap;
</script>
</body>
</html>
