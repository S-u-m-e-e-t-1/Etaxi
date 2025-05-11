<?php
require_once '../../controllers/StatisticsController.php';

$controller = new StatisticsController();
$data = $controller->getReportData();

// Extract data for view
$currentYear = $data['currentYear'];
$currentMonth = $data['currentMonth'];
$year = $data['year'];
$month = $data['month'];
$monthlyStats = $data['monthlyStats'];
$yearlyStats = $data['yearlyStats'];
$months = $data['months'];
$generatedDate = $data['generatedDate'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Report - ETaxi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        @media print {
            .no-print {
                display: none;
            }
            .print-only {
                display: block;
            }
        }
        .print-only {
            display: none;
        }
        .stat-card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h2>Monthly Report</h2>
                <p class="text-muted">Generated on <?php echo $generatedDate; ?></p>
            </div>
            <div class="col text-end no-print">
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Print Report
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card mb-4 no-print">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Year</label>
                        <select name="year" class="form-select">
                            <?php for($i = $currentYear; $i >= $currentYear - 2; $i--): ?>
                                <option value="<?php echo $i; ?>" <?php echo $year == $i ? 'selected' : ''; ?>>
                                    <?php echo $i; ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Month</label>
                        <select name="month" class="form-select">
                            <?php foreach($months as $num => $name): ?>
                                <option value="<?php echo $num; ?>" <?php echo $month == $num ? 'selected' : ''; ?>>
                                    <?php echo $name; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary">Generate Report</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Rides</h5>
                        <h2 class="card-text"><?php echo number_format($monthlyStats['rides']); ?></h2>
                        <p class="card-text">Total Distance: <?php echo number_format($monthlyStats['distance'], 2); ?> km</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Earnings</h5>
                        <h2 class="card-text">₹<?php echo number_format($monthlyStats['amount'], 2); ?></h2>
                        <p class="card-text">Total Payments: <?php echo number_format($monthlyStats['payments']); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Customers</h5>
                        <h2 class="card-text"><?php echo number_format($monthlyStats['customers']); ?></h2>
                        <p class="card-text">Active Users</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Average Fare</h5>
                        <h2 class="card-text">₹<?php echo $monthlyStats['rides'] > 0 ? number_format($monthlyStats['amount'] / $monthlyStats['rides'], 2) : '0.00'; ?></h2>
                        <p class="card-text">Per Ride</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Rides Trend</h5>
                        <div class="chart-container">
                            <canvas id="ridesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Monthly Earnings Trend</h5>
                        <div class="chart-container">
                            <canvas id="earningsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Table -->
        <div class="card mt-4">
            <div class="card-body">
                <h5 class="card-title">Monthly Breakdown</h5>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Rides</th>
                                <th>Total Distance (km)</th>
                                <th>Total Earnings</th>
                                <th>Total Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($yearlyStats as $monthNum => $monthData): ?>
                            <tr>
                                <td><?php echo $months[$monthNum]; ?></td>
                                <td><?php echo number_format($monthData['rides']); ?></td>
                                <td><?php echo number_format($monthData['distance'], 2); ?></td>
                                <td>₹<?php echo number_format($monthData['amount'], 2); ?></td>
                                <td><?php echo number_format($monthData['customers']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prepare data for charts
        const months = <?php echo json_encode(array_values($months)); ?>;
        const yearlyStats = <?php echo json_encode($yearlyStats); ?>;
        
        // Rides Chart
        const ridesCtx = document.getElementById('ridesChart').getContext('2d');
        new Chart(ridesCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Rides',
                    data: Object.values(yearlyStats).map(stat => stat.rides),
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Earnings Chart
        const earningsCtx = document.getElementById('earningsChart').getContext('2d');
        new Chart(earningsCtx, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    label: 'Total Earnings',
                    data: Object.values(yearlyStats).map(stat => stat.amount),
                    borderColor: 'rgb(153, 102, 255)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html>