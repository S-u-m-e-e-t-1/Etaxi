<?php


require_once '../../controllers/DriverController.php';

$driverData = $_SESSION['driver'];
$controller = new DriverController();
$messages = $controller->getMessages($driverData['email']);
$sentMessages = $messages['sentMessages'];
$receivedMessages = $messages['receivedMessages'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Messages</title>
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
                            <th>To Customer</th>
                            <th>Content</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($sentMessages) && is_array($sentMessages)): ?>
                            <?php foreach ($sentMessages as $message): ?>
                                <tr data-message-id="<?= $message['id'] ?>" data-sender="<?= $message['sender'] ?>" data-receiver="<?= $message['receiver'] ?>" data-content="<?= $message['content'] ?>" data-status="<?= $message['status'] ?>" data-created-at="<?= $message['created_at'] ?>">
                                    <td><?= $message['id'] ?></td>
                                    <td><?= $message['receiver'] ?></td>
                                    <td><?= $message['content'] ?></td>
                                    <td><?= $message['created_at'] ?></td>
                                    <td><?= $message['status'] ?></td>
                                    <td><button class="btn btn-primary btn-sm" onclick="viewDetails(<?= $message['id'] ?>)">View Details</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No sent messages found.</td>
                            </tr>
                        <?php endif; ?>
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
                            <th>From Customer</th>
                            <th>Content</th>
                            <th>Created At</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($receivedMessages) && is_array($receivedMessages)): ?>
                            <?php foreach ($receivedMessages as $message): ?>
                                <tr data-message-id="<?= $message['id'] ?>" data-sender="<?= $message['sender'] ?>" data-receiver="<?= $message['receiver'] ?>" data-content="<?= $message['content'] ?>" data-status="<?= $message['status'] ?>" data-created-at="<?= $message['created_at'] ?>">
                                    <td><?= $message['id'] ?></td>
                                    <td><?= $message['sender'] ?></td>
                                    <td><?= $message['content'] ?></td>
                                    <td><?= $message['created_at'] ?></td>
                                    <td><?= $message['status'] ?></td>
                                    <td><button class="btn btn-primary btn-sm" onclick="viewDetails(<?= $message['id'] ?>)">View Details</button></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">No received messages found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <button class="btn btn-success mb-3" onclick="openSendMessagePopup()">Send Message</button>

        <!-- Send Message Modal -->
        <div id="sendMessagePopup" class="modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Send Message to Customer</h5>
                        <button type="button" class="close" onclick="closeSendMessagePopup()" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="sendMessageForm" onsubmit="sendMessage(event)">
                            <div class="form-group">
                                <label for="receiverEmail">Customer Email:</label>
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
            if (!row) return;

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

            // Update message status to read
            fetch('../../controllers/DriverController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'updateMessageStatus', 
                    messageId: messageId 
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update status in the table
                    const statusCell = row.querySelector('[data-status]');
                    if (statusCell) {
                        statusCell.textContent = 'read';
                        statusCell.dataset.status = 'read';
                    }
                } else {
                    alert(data.message || 'Failed to update message status.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the message status.');
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
            const senderEmail = '<?php echo $driverData['email']; ?>';
            const receiverEmail = document.getElementById("receiverEmail").value;
            const content = document.getElementById("messageContent").value;

            fetch('../../controllers/DriverController.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ 
                    action: 'sendMessage', 
                    senderEmail: senderEmail, 
                    receiverEmail: receiverEmail, 
                    content: content
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Message sent successfully.');
                    closeSendMessagePopup();
                    location.reload();
                } else {
                    alert('Failed to send message: ' + (data.error || 'Unknown error'));
                }
            });
        }
    </script>
</body>
</html>
