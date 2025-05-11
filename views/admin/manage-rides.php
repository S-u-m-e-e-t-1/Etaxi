<?php
require_once __DIR__ . '/../../controllers/AdminController.php';
if (!isset($_SESSION['admin'])) {
    header('Location: ../../login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rides</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function filterRides() {
            const status = document.getElementById('status').value;
            const search = document.getElementById('search').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach(row => {
                const rideStatus = row.querySelector('.ride-status').textContent.toLowerCase();
                const pickupLocation = row.querySelector('.pickup-location').textContent.toLowerCase();
                const dropoffLocation = row.querySelector('.dropoff-location').textContent.toLowerCase();

                const matchesStatus = !status || rideStatus === status;
                const matchesSearch = !search || pickupLocation.includes(search) || dropoffLocation.includes(search);

                if (matchesStatus && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        function viewDetails(rideId) {
            $.ajax({
                url: '../../controllers/AdminController.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ action: 'getRideDetails', rideId: rideId }),
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        const ride = data.ride;
                        const reviews = data.reviews;
                        let reviewsHtml = '';
                        reviews.forEach(review => {
                            reviewsHtml += `
                                <div class="review">
                                    <p>Rating: ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}</p>
                                    <p>Review: ${review.review}</p>
                                    <p>Created At: ${review.created_at}</p>
                                </div>
                                <hr>
                            `;
                        });

                        $('#rideDetails').html(`
                            <h5>Ride Details</h5>
                            <p>ID: ${ride.id}</p>
                            <p>Pickup Location: ${ride.pickup_location}</p>
                            <p>Dropoff Location: ${ride.dropoff_location}</p>
                            <p>Status: ${ride.ride_status}</p>
                            <p>Fare: ${ride.fare}</p>
                            <p>Request Time: ${ride.request_time}</p>
                            <p>Distance: ${ride.distance}</p>
                            <p>Rate: ${ride.rate}</p>
                            <h5>Customer Details</h5>
                            <p>Name: ${ride.customer_name}</p>
                            <p>Email: ${ride.customer_email}</p>
                            <p>Phone: ${ride.customer_phone}</p>
                            <h5>Driver Details</h5>
                            <p>Name: ${ride.driver_name}</p>
                            <p>Email: ${ride.driver_email}</p>
                            <p>Phone: ${ride.driver_phone}</p>
                            <h5>Reviews</h5>
                            ${reviewsHtml}
                        `);
                        $('#detailsModal').modal('show');
                    } else {
                        alert('Failed to fetch ride details');
                    }
                }
            });
        }
    </script>
    <style>
        .table-wrapper {
            height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Manage Rides</h1>
        <form onsubmit="event.preventDefault(); filterRides();">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" onchange="filterRides()">
                <option value="">All</option>
                <option value="pending">Pending</option>
                <option value="accepted">Accepted</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <label for="search">Search:</label>
            <input type="text" name="search" id="search" onkeyup="filterRides()">
            <button type="submit">Apply</button>
        </form>
        <div class="table-wrapper">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Customer ID</th>
                        <th class="pickup-location">Pickup Location</th>
                        <th class="dropoff-location">Dropoff Location</th>
                        <th class="ride-status">Status</th>
                        <th>Fare</th>
                        <th>Driver ID</th>
                        <th>Request Time</th>
                        <th>Distance</th>
                        <th>Rate</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rides as $ride): ?>
                        <tr>
                            <td><?php echo $ride['id']; ?></td>
                            <td><?php echo $ride['customer_id']; ?></td>
                            <td class="pickup-location"><?php echo $ride['pickup_location']; ?></td>
                            <td class="dropoff-location"><?php echo $ride['dropoff_location']; ?></td>
                            <td class="ride-status"><?php echo $ride['ride_status']; ?></td>
                            <td><?php echo $ride['fare']; ?></td>
                            <td><?php echo $ride['driver_id']; ?></td>
                            <td><?php echo $ride['request_time']; ?></td>
                            <td><?php echo $ride['distance']; ?></td>
                            <td><?php echo $ride['rate']; ?></td>
                            <td>
                                <button class="btn btn-primary" onclick="viewDetails(<?php echo $ride['id']; ?>)">View Details</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Popup Modal for Viewing Details -->
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Ride Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="rideDetails">
                        <!-- Ride details will be loaded here via AJAX -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>