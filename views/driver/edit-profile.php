<?php

require_once '../../controllers/DriverController.php';


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Edit Profile</h2>

        <!-- Display error or success messages -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <!-- Driver Information Form -->
        <form id="driverForm" enctype="multipart/form-data" class="mb-5">
            <input type="hidden" name="action" value="updateDriverProfile">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Driver Information</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name" value="<?= htmlspecialchars($profile['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($profile['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>ID Number</label>
                        <input type="text" class="form-control" name="id_number" value="<?= htmlspecialchars($profile['id_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Profile Image</label>
                        <img src="../../uploads/drivers/profile/<?= htmlspecialchars($profile['profile_image']) ?>" class="img-thumbnail mb-2" alt="Profile Image">
                        <input type="file" class="form-control" name="profile_image" accept="image/*">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-block">Save Driver Info</button>
        </form>

        <!-- Vehicle Information Form -->
        <form id="vehicleForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="updateVehicleProfile">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">Vehicle Information</div>
                <div class="card-body">
                    <div class="form-group">
                        <label>License Number</label>
                        <input type="text" class="form-control" name="license_number" value="<?= htmlspecialchars($profile['license_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Type</label>
                        <input type="text" class="form-control" name="vehicle_type" value="<?= htmlspecialchars($profile['vehicle_type']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Model</label>
                        <input type="text" class="form-control" name="vehicle_model" value="<?= htmlspecialchars($profile['vehicle_model']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Number</label>
                        <input type="text" class="form-control" name="vehicle_number" value="<?= htmlspecialchars($profile['vehicle_number']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Vehicle Image</label>
                        <img src="../../uploads/drivers/vehicle/<?= htmlspecialchars($profile['vehicle_image']) ?>" class="img-thumbnail mb-2" alt="Vehicle Image">
                        <input type="file" class="form-control" name="vehicle_image" accept="image/*">
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-success btn-block">Save Vehicle Info</button>
        </form>
    </div>

    <script>
        document.getElementById('driverForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../controllers/DriverController.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Driver information updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update driver information: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('vehicleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            fetch('../../controllers/DriverController.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Vehicle information updated successfully!');
                    location.reload();
                } else {
                    alert('Failed to update vehicle information: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>