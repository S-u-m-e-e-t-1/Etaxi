<?php include 'includes/header.php'; ?>
<?php
include 'controllers/StatisticsController.php';
$controller = new StatisticsController();
$stats = $controller->showStats();
?>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-header">Total Drivers</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['totalDrivers']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-header">Total Payments</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['totalPayments']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-header">Total Kilometers</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['totalKilometers']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-header">Total Customers</div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo $stats['totalCustomers']; ?></h5>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>