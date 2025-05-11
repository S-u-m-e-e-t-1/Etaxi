<?php
require_once '../../controllers/DriverController.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Earnings - ETaxi</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">My Earnings</h4>
                    <span class="badge bg-light text-primary">
                        Total Earnings: <?php 
                            $total = array_sum(array_column($earnings, 'amount'));
                            echo '₹' . number_format($total, 2);
                        ?>
                    </span>
                </div>
            </div>
            <div class="card-body">
                <?php if (count($earnings) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center">Payment ID</th>
                                    <th class="text-center">Ride ID</th>
                                    <th class="text-center">Amount</th>
                                    <th class="text-center">Payment Method</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($earnings as $payment): ?>
                                    <tr>
                                        <td class="text-center"><?php echo htmlspecialchars($payment['payment_id']); ?></td>
                                        <td class="text-center"><?php echo htmlspecialchars($payment['ride_id']); ?></td>
                                        <td class="text-center text-success fw-bold">
                                            <?php echo '₹' . number_format($payment['amount'], 2); ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($payment['payment_method']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge <?php 
                                                echo $payment['payment_status'] === 'completed' ? 'bg-success' : 
                                                    ($payment['payment_status'] === 'pending' ? 'bg-warning' : 'bg-danger'); 
                                            ?>">
                                                <?php echo htmlspecialchars($payment['payment_status']); ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php 
                                                $date = new DateTime($payment['payment_date']);
                                                echo $date->format('d M Y, h:i A');
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>No earnings found.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include '../../includes/footer.php'; ?>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
