<?php
require_once '../../controllers/AdminController.php';
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
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" onkeyup="searchNotifications()" placeholder="Search for notifications..">
        </div>
        <button class="btn btn-success mb-3" onclick="openAddNotificationPopup()">Add Notification</button>
        <div class="table-responsive">
            <table id="notificationsTable" class="table table-striped table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Message</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notifications as $notification): ?>
                        <tr>
                            <td><?= $notification['id'] ?></td>
                            <td><?= $notification['message'] ?></td>
                            <td><?= $notification['created_at'] ?></td>
                            <td>
                                <button class="btn btn-primary btn-sm" onclick="openEditNotificationPopup(<?= $notification['id'] ?>, '<?= $notification['message'] ?>')">Edit</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteNotification(<?= $notification['id'] ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="addNotificationPopup" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Notification</h5>
                        <button type="button" class="close" onclick="closeAddNotificationPopup()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addNotificationForm" onsubmit="addNotification(event)">
                            <div class="form-group">
                                <label for="notificationMessage">Message:</label>
                                <textarea id="notificationMessage" name="notificationMessage" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add</button>
                            <button type="button" class="btn btn-secondary" onclick="closeAddNotificationPopup()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="editNotificationPopup" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Notification</h5>
                        <button type="button" class="close" onclick="closeEditNotificationPopup()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="editNotificationForm" onsubmit="updateNotification(event)">
                            <input type="hidden" id="editNotificationId" name="editNotificationId">
                            <div class="form-group">
                                <label for="editNotificationMessage">Message:</label>
                                <textarea id="editNotificationMessage" name="editNotificationMessage" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Update</button>
                            <button type="button" class="btn btn-secondary" onclick="closeEditNotificationPopup()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchNotifications() {
            var input, filter, table, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            table = document.getElementById("notificationsTable");
            tr = table.getElementsByTagName("tr");
            for (i = 1; i < tr.length; i++) {
                tr[i].style.display = "none";
                td = tr[i].getElementsByTagName("td");
                for (j = 0; j < td.length; j++) {
                    if (td[j]) {
                        txtValue = td[j].textContent || td[j].innerText;
                        if (txtValue.toLowerCase().indexOf(filter) > -1) {
                            tr[i].style.display = "";
                            break;
                        }
                    }
                }
            }
        }

        function openAddNotificationPopup() {
            $('#addNotificationPopup').modal('show');
        }

        function closeAddNotificationPopup() {
            $('#addNotificationPopup').modal('hide');
        }

        function openEditNotificationPopup(id, message) {
            document.getElementById("editNotificationId").value = id;
            document.getElementById("editNotificationMessage").value = message;
            $('#editNotificationPopup').modal('show');
        }

        function closeEditNotificationPopup() {
            $('#editNotificationPopup').modal('hide');
        }

        function addNotification(event) {
            event.preventDefault();
            const message = document.getElementById("notificationMessage").value;

            fetch('../../controllers/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'addNotification', 
                    message: message
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notification added successfully.');
                    location.reload();
                } else {
                    alert('Failed to add notification.');
                }
            });
        }

        function updateNotification(event) {
            event.preventDefault();
            const id = document.getElementById("editNotificationId").value;
            const message = document.getElementById("editNotificationMessage").value;

            fetch('../../controllers/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'updateNotification', id: id, message: message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Notification updated successfully.');
                    location.reload();
                } else {
                    alert('Failed to update notification.');
                }
            });
        }

        function deleteNotification(id) {
            if (confirm('Are you sure you want to delete this notification?')) {
                fetch('../../controllers/AdminController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ action: 'deleteNotification', id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Notification deleted successfully.');
                        location.reload();
                    } else {
                        alert('Failed to delete notification.');
                    }
                });
            }
        }
    </script>
</body>
</html>