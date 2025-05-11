<?php
session_start();
if (!isset($_SESSION['driver'])) {
    header('Location: ../../login.php');
    exit;
}

require_once '../../controllers/RideController.php';
$controller = new RideController();
$driver_id = $_SESSION['driver']['id'];

// Fetch rides using the controller
$accepted_rides = $controller->getDriverRides($driver_id, 'accepted');
$paid_rides = $controller->getDriverRides($driver_id, 'paid');
$completed_rides = $controller->getDriverRides($driver_id, 'completed');

// Handle errors
if (!$accepted_rides['success'] || !$completed_rides['success']) {
    $_SESSION['error'] = "Failed to fetch rides data";
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Rides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
</head>
<body>
    <div class="container mt-5">
        <!-- Error/Success Messages -->
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

       

        <!-- Accepted Rides Table -->
        <div class="card mb-4">
            <div class="card-header">
                <h3>Paid Rides</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="acceptedRidesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Fare</th>
                            <th>Distance</th>
                            <th>Request Time</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($paid_rides['rides'] as $ride): ?>
                            <tr>
                                <td><?= htmlspecialchars($ride['id']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_name']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($ride['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($ride['dropoff_location']) ?></td>
                                <td>₹<?= htmlspecialchars($ride['fare']) ?></td>
                                <td><?= htmlspecialchars($ride['distance']) ?> km</td>
                                <td><?= htmlspecialchars($ride['request_time']) ?></td>
                                <td>
                                    <button class="btn btn-success btn-sm" 
                                            onclick="completeRide(<?= $ride['id'] ?>)">
                                        Complete Ride
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <h3>Accepted Rides</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="acceptedRidesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Fare</th>
                            <th>Distance</th>
                            <th>Request Time</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accepted_rides['rides'] as $ride): ?>
                            <tr>
                                <td><?= htmlspecialchars($ride['id']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_name']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($ride['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($ride['dropoff_location']) ?></td>
                                <td>₹<?= htmlspecialchars($ride['fare']) ?></td>
                                <td><?= htmlspecialchars($ride['distance']) ?> km</td>
                                <td><?= htmlspecialchars($ride['request_time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Completed Rides Table -->
        <div class="card">
            <div class="card-header">
                <h3>Completed Rides</h3>
            </div>
            <div class="card-body">
                <table class="table table-striped" id="completedRidesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer</th>
                            <th>Phone</th>
                            <th>Pickup</th>
                            <th>Dropoff</th>
                            <th>Fare</th>
                            <th>Distance</th>
                            <th>Completed Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($completed_rides['rides'] as $ride): ?>
                            <tr>
                                <td><?= htmlspecialchars($ride['id']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_name']) ?></td>
                                <td><?= htmlspecialchars($ride['customer_phone']) ?></td>
                                <td><?= htmlspecialchars($ride['pickup_location']) ?></td>
                                <td><?= htmlspecialchars($ride['dropoff_location']) ?></td>
                                <td>₹<?= htmlspecialchars($ride['fare']) ?></td>
                                <td><?= htmlspecialchars($ride['distance']) ?> km</td>
                                <td><?= htmlspecialchars($ride['complete_time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- OTP Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter OTP to Complete Ride</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="otpInput">Enter OTP received from customer</label>
                        <input type="text" id="otpInput" class="form-control" 
                               placeholder="Enter 6-digit OTP" maxlength="6" pattern="\d{6}">
                        <div class="invalid-feedback">Please enter a valid 6-digit OTP</div>
                    </div>
                    <input type="hidden" id="rideId">
                    <div id="otpError" class="alert alert-danger d-none"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="verifyOTP()">Verify & Complete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#acceptedRidesTable').DataTable({
                order: [[7, 'desc']]
            });
            $('#completedRidesTable').DataTable({
                order: [[7, 'desc']]
            });
        });

        function completeRide(rideId) {
            $('#rideId').val(rideId);
            $('#otpInput').val('');
            $('#otpError').addClass('d-none');
            $('#otpInput').removeClass('is-invalid');
            $('#otpModal').modal('show');
        }

        function verifyOTP() {
            const rideId = $('#rideId').val();
            const otp = $('#otpInput').val();
            const errorDiv = $('#otpError');
            
            // Reset previous error states
            $('#otpInput').removeClass('is-invalid');
            errorDiv.addClass('d-none');
            
            // Validate OTP format
            if (!otp || !/^\d{6}$/.test(otp)) {
                $('#otpInput').addClass('is-invalid');
                return;
            }

            // Disable the verify button to prevent double submission
            const verifyBtn = $('.modal-footer .btn-primary');
            verifyBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Verifying...');

            fetch('../../controllers/RideController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=completeRide&ride_id=${rideId}&driver_id=<?= $driver_id ?>&otp=${otp}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    errorDiv.removeClass('d-none').text(data.error || 'Failed to complete ride');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                errorDiv.removeClass('d-none').text('Error completing ride. Please try again.');
            })
            .finally(() => {
                verifyBtn.prop('disabled', false).text('Verify & Complete');
            });
        }
    </script>
</body>
</html>
