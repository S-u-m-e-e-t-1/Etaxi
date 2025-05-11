<?php

require_once __DIR__ . '/../../controllers/AdminController.php';
// Check if admin is logged in
if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .scrollable-table {
            max-height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Admin Dashboard</h2>

    <!-- Dashboard Summary -->
    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Rides</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $totalRides ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Earnings</div>
                <div class="card-body">
                    <h4 class="card-title">₹<?= number_format($totalEarnings, 2) ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Active Drivers</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $activeDrivers ?></h4>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card text-white bg-info mb-3">
                <div class="card-header">Registered Customers</div>
                <div class="card-body">
                    <h4 class="card-title"><?= $registeredCustomers ?></h4>
                </div>
            </div>
        </div>
        
    </div>

    <!-- Recent Rides -->
    <div class="card mt-4 scrollable-table">
        <div class="card-header">
            <h4>Recent Rides</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped ">
                    <thead>
                        <tr>
                            <th>Ride ID</th>
                            <th>Driver</th>
                            <th>Customer</th>
                            <th>Pickup</th>
                            <th>Drop-off</th>
                            <th>Status</th>
                            <th>Earnings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rides as $ride): ?>
                        <tr>
                            <td>#<?= $ride['id'] ?></td>
                            <td><?= $ride['driver_id'] ?></td>
                            <td><?= $ride['customer_id'] ?></td>
                            <td><?= $ride['pickup_location'] ?></td>
                            <td><?= $ride['dropoff_location'] ?></td>
                            <td><span class="badge bg-<?= $ride['ride_status'] == 'completed' ? 'success' : ($ride['ride_status'] == 'ongoing' ? 'primary' : 'danger') ?>"><?= ucfirst($ride['ride_status']) ?></span></td>
                            <td>₹<?= $ride['fare'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
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
            <iframe src="https://maps.google.com/maps?q=Hinjilicut&t=&z=13&ie=UTF8&iwloc=&output=embed" 
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
