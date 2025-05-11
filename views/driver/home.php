<?php

require_once '../../controllers/DriverController.php';

if (!isset($_SESSION['driver'])) {
    header('Location: ../../login.php');
    exit;
}

$driverId = $_SESSION['driver']['id'];
$controller = new DriverController();

// Fetch data for the home page
$data = $controller->getHomePageData($driverId);
$dashboardData = $data['dashboardData'];
$isAvailable = $data['availability'];

// Handle accept ride action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accept_ride_id'])) {
    $rideId = $_POST['accept_ride_id'];
    $result = $controller->handleAcceptRide($rideId, $driverId);

    if (!$result['success']) {
        $_SESSION['error'] = $result['error'];
    } else {
        $_SESSION['success'] = $result['message'];
    }

    header('Location: home.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Driver Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="container mt-4">
    <!-- Dashboard Summary -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Rides</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $dashboardData['totalRides'] ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Earnings</div>
                <div class="card-body">
                    <h4 class="card-title">₹<?= number_format($dashboardData['totalEarnings'], 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card <?= $isAvailable ? 'bg-success' : 'bg-danger' ?> text-white">
                <div class="card-header">Availability Status</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $isAvailable ? 'Available' : 'Unavailable' ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Display error or success messages -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION['error']) ?>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?= htmlspecialchars($_SESSION['success']) ?>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>

    <!-- Pending Rides -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Pending Rides</h4>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Ride ID</th>
                        <th>Customer</th>
                        <th>Pickup</th>
                        <th>Drop-off</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dashboardData['pendingRides'] as $ride): ?>
                    <tr>
                        <td><?= htmlspecialchars($ride['id']) ?></td>
                        <td><?= htmlspecialchars($ride['customer_id']) ?></td>
                        <td><?= htmlspecialchars($ride['pickup_location']) ?></td>
                        <td><?= htmlspecialchars($ride['dropoff_location']) ?></td>
                        <td>
                            <form method="POST">
                                <input type="hidden" name="action" value="acceptRide">
                                <input type="hidden" name="ride_id" value="<?= htmlspecialchars($ride['id']) ?>">
                                <input type="hidden" name="driver_id" value="<?= htmlspecialchars($driverId) ?>">
                                <button type="submit" class="btn btn-success">Accept</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Earnings Chart -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Earnings Overview</h4>
        </div>
        <div class="card-body">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Google Maps Live Tracking (Placeholder) -->
    <div class="card mt-4">
        <div class="card-header">
            <h4>Live Driver Tracking</h4>
        </div>
        <div class="card-body">
            <iframe src="https://maps.google.com/maps?q=Dhabalpur&t=&z=13&ie=UTF8&iwloc=&output=embed" 
                    width="100%" height="300px" frameborder="0"></iframe>
        </div>
    </div>
</div>

<!-- Chart.js Script -->
<script>
    var ctx = document.getElementById('earningsChart').getContext('2d');
    var earningsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Earnings ($)',
                data: [5000, 8000, 7500, 12000, 15000, 18000],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2
            }]
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
