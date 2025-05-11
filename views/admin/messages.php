<?php

require_once '../../controllers/AdminController.php';
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
    <title>Messages</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Messages</h1>
        <div class="mb-3">
            <input type="text" id="searchInput" class="form-control" onkeyup="searchMessages()" placeholder="Search for messages..">
        </div>
        <div class="messages-section mb-5">
            <h2>Sent Messages</h2>
            <div class="table-responsive">
                <table id="sentMessagesTable" class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Receiver</th>
                            <th>Content</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sentMessages as $message): ?>
                            <tr data-message-id="<?= $message['id'] ?>">
                                <td><?= $message['id'] ?></td>
                                <td data-receiver="<?= htmlspecialchars($message['receiver']) ?>"><?= $message['receiver'] ?></td>
                                <td data-content="<?= htmlspecialchars($message['content']) ?>"><?= $message['content'] ?></td>
                                <td data-created-at="<?= htmlspecialchars($message['created_at']) ?>"><?= $message['created_at'] ?></td>
                                <td data-status="<?= htmlspecialchars($message['status']) ?>"><?= $message['status'] ?></td>
                                <td><button class="btn btn-primary btn-sm" onclick="viewDetails(<?= $message['id'] ?>)">View Details</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="messages-section mb-5">
            <h2>Received Messages</h2>
            <div class="table-responsive">
                <table id="receivedMessagesTable" class="table table-striped table-bordered">
                    <thead class="thead-dark">
                        <tr>
                            <th>ID</th>
                            <th>Sender</th>
                            <th>Content</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receivedMessages as $message): ?>
                            <tr data-message-id="<?= $message['id'] ?>">
                                <td><?= $message['id'] ?></td>
                                <td data-sender="<?= htmlspecialchars($message['sender']) ?>"><?= $message['sender'] ?></td>
                                <td data-content="<?= htmlspecialchars($message['content']) ?>"><?= $message['content'] ?></td>
                                <td data-created-at="<?= htmlspecialchars($message['created_at']) ?>"><?= $message['created_at'] ?></td>
                                <td data-status="<?= htmlspecialchars($message['status']) ?>"><?= $message['status'] ?></td>
                                <td><button class="btn btn-primary btn-sm" onclick="viewDetails(<?= $message['id'] ?>)">View Details</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-success mb-3" onclick="openSendMessagePopup()">Send Message</button>

        <div id="sendMessagePopup" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Message</h5>
                        <button type="button" class="close" onclick="closeSendMessagePopup()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="sendMessageForm" onsubmit="sendMessage(event)">
                            <div class="form-group">
                                <label for="receiverEmail">Receiver Email:</label>
                                <input type="email" id="receiverEmail" name="receiverEmail" class="form-control" required>
                            </div>
                            <div class="form-group">
                                <label for="messageContent">Message:</label>
                                <textarea id="messageContent" name="messageContent" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Send</button>
                            <button type="button" class="btn btn-secondary" onclick="closeSendMessagePopup()">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="messageDetailsModal" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Message Details</h5>
                        <button type="button" class="close" onclick="closeMessageDetailsModal()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label><strong>Sender:</strong></label>
                            <p id="messageDetailsSender"></p>
                        </div>
                        <div class="form-group">
                            <label><strong>Receiver:</strong></label>
                            <p id="messageDetailsReceiver"></p>
                        </div>
                        <div class="form-group">
                            <label><strong>Content:</strong></label>
                            <p id="messageDetailsContent"></p>
                        </div>
                        <div class="form-group">
                            <label><strong>Status:</strong></label>
                            <p id="messageDetailsStatus"></p>
                        </div>
                        <div class="form-group">
                            <label><strong>Created At:</strong></label>
                            <p id="messageDetailsCreatedAt"></p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeMessageDetailsModal()">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchMessages() {
            var input, filter, tables, tr, td, i, j, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toLowerCase();
            tables = [document.getElementById("sentMessagesTable"), document.getElementById("receivedMessagesTable")];
            for (var t = 0; t < tables.length; t++) {
                tr = tables[t].getElementsByTagName("tr");
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
        }

        function viewDetails(messageId) {
            // Find the message row
            const row = document.querySelector(`tr[data-message-id="${messageId}"]`);
            const messageData = {
                sender: row.querySelector('[data-sender]')?.dataset.sender,
                receiver: row.querySelector('[data-receiver]')?.dataset.receiver,
                content: row.querySelector('[data-content]').dataset.content,
                status: row.querySelector('[data-status]').dataset.status,
                createdAt: row.querySelector('[data-created-at]').dataset.createdAt
            };

            // Update modal content
            document.getElementById('messageDetailsSender').textContent = messageData.sender || 'N/A';
            document.getElementById('messageDetailsReceiver').textContent = messageData.receiver || 'N/A';
            document.getElementById('messageDetailsContent').textContent = messageData.content;
            document.getElementById('messageDetailsStatus').textContent = messageData.status;
            document.getElementById('messageDetailsCreatedAt').textContent = messageData.createdAt;

            // Show modal
            $('#messageDetailsModal').modal('show');

            // Update message status to seen
            fetch('../../controllers/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'viewMessageDetails',
                    messageId: messageId 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update status in the table
                    const statusCell = row.querySelector('[data-status]');
                    if (statusCell) {
                        statusCell.textContent = 'seen';
                        statusCell.dataset.status = 'seen';
                    }
                }
            });
        }

        function closeMessageDetailsModal() {
            $('#messageDetailsModal').modal('hide');
        }

        function openSendMessagePopup() {
            $('#sendMessagePopup').modal('show');
        }

        function closeSendMessagePopup() {
            $('#sendMessagePopup').modal('hide');
        }

        function sendMessage(event) {
            event.preventDefault();
            const senderEmail = '<?php echo getAdminEmail(); ?>'; // Fetch the admin email from the controller
            const receiverEmail = document.getElementById("receiverEmail").value;
            const content = document.getElementById("messageContent").value;

            fetch('../../controllers/AdminController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ action: 'sendMessage', senderEmail: senderEmail, receiverEmail: receiverEmail, content: content })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully.');
                    closeSendMessagePopup();
                } else {
                    alert('Failed to send message.');
                }
            });
        }
    </script>
</body>
</html>