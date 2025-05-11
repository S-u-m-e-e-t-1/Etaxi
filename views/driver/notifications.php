<?php
require_once '../../controllers/DriverController.php';
$controller = new DriverController();
$driver_id = $_SESSION['driver']['id'];
$result = $controller->getDriverNotifications();
$notifications = $result['success'] ? $result['notifications'] : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Notifications</h1>
        <div class="row">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $notification): ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Notification #<?= htmlspecialchars($notification['id']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($notification['message']) ?></p>
                                <p class="text-muted"><small>Created At: <?= htmlspecialchars($notification['created_at']) ?></small></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">No notifications found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
