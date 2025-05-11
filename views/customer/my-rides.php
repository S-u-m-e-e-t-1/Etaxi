<?php
require_once '../../controllers/CustomerController.php';

// Check if customer is logged in
if (!isset($_SESSION['customer'])) {
    header('Location: ../../login.php');
    exit;
}
$customerData = $_SESSION['customer'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Rides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">My Rides</h1>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Driver</th>
                        <th>Pickup Location</th>
                        <th>Dropoff Location</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rides as $ride): ?>
                        <tr>
                            <td><?= $ride['id'] ?></td>
                            <td><?= $ride['driver_name'] ?></td>
                            <td><?= $ride['pickup_location'] ?></td>
                            <td><?= $ride['dropoff_location'] ?></td>
                            <td><?= $ride['ride_status'] ?></td>
                            <td>
                                <?php if ($ride['ride_status'] === 'pending'): ?>
                                    <button class="btn btn-danger btn-sm" onclick="cancelRide(<?= $ride['id'] ?>)">Cancel</button>
                                <?php elseif ($ride['ride_status'] === 'accepted'): ?>
                                    <button class="btn btn-primary btn-sm" onclick="openReviewModal(<?= $ride['id'] ?>, <?= $ride['driver_id'] ?>)">Review</button>
                                    <button class="btn btn-success btn-sm" onclick="makePayment(<?= $ride['id'] ?>,<?= $ride['fare'] ?>)">Pay</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Review Ride</h5>
                    <button type="button" class="close" onclick="closeReviewModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" onsubmit="submitReview(event)">
                        <input type="hidden" id="rideId" name="rideId">
                        <input type="hidden" id="driverId" name="driverId">
                        <div class="form-group">
                            <label for="rating">Rating:</label>
                            <input type="number" id="rating" name="rating" class="form-control" min="1" max="5" required>
                        </div>
                        <div class="form-group">
                            <label for="review">Review:</label>
                            <textarea id="review" name="review" class="form-control" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Cancel</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function cancelRide(rideId) {
            if (!confirm('Are you sure you want to cancel this ride?')) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'cancelRide');
            formData.append('ride_id', rideId);

            fetch('../../controllers/RideController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Ride cancelled successfully.');
                    location.reload();
                } else {
                    alert('Failed to cancel ride: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while cancelling the ride. Please try again.');
            });
        }

        function openReviewModal(rideId, driverId) {
            document.getElementById('rideId').value = rideId;
            document.getElementById('driverId').value = driverId;
            $('#reviewModal').modal('show');
        }

        function closeReviewModal() {
            $('#reviewModal').modal('hide');
        }

        function submitReview(event) {
            event.preventDefault();

            const formData = new FormData(document.getElementById('reviewForm'));
            formData.append('action', 'submitReview');

            fetch('../../controllers/CustomerController.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Review submitted successfully.');
                    closeReviewModal();
                    location.reload();
                } else {
                    console.error('Failed to submit review:', data.error);
                    alert('Failed to submit review: ' + data.error);
                }
            })
            .catch(error => {
                console.error('An error occurred:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function makePayment(rideId, rideFare) {
            // Redirect to the payment page with the ride ID and fare
                window.location.href = '/ETaxi/payment/pay.php?ride_id=' + rideId + '&ride_fare=' + rideFare;
            }
    </script>
</body>
</html>