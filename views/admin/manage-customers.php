<?php
// Include the AdminController to get the $customers array
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .scrollable-table {
            max-height: 400px;
            overflow-y: scroll;
            border: 1px solid #ddd;
        }
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Customers</h1>
        <div id="alertContainer"></div>
        <input class="form-control mb-3" id="searchInput" type="text" placeholder="Search by name...">
        <div class="scrollable-table">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>Name</th>
                        <th>Profile Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="customerTable">
                    <?php if (isset($customers) && !empty($customers)): ?>
                        <?php foreach ($customers as $customer): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($customer['name']); ?></td>
                                <td><img src="<?php echo '../../uploads/users/profile/' . htmlspecialchars($customer['profile_image']); ?>" alt="Profile Image" width="50"></td>
                                <td>
                                    <button class="btn btn-info btn-sm view-details" data-customer='<?php echo json_encode($customer); ?>'>View Details</button>
                                    <button class="btn btn-danger btn-sm delete-customer" data-customer-id="<?php echo htmlspecialchars($customer['id']); ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">No customers found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalLabel">Customer Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>Name:</strong> <span id="modalCustomerName"></span></p>
                    <p><strong>Email:</strong> <span id="modalCustomerEmail"></span></p>
                    <p><strong>Phone:</strong> <span id="modalCustomerPhone"></span></p>
                    <p><strong>Profile Image:</strong> <img id="modalCustomerImage" src="" alt="Profile Image" width="50"></p>
                    <p><strong>Created At:</strong> <span id="modalCustomerCreatedAt"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input = this.value.toLowerCase();
            var rows = document.querySelectorAll('#customerTable tr');
            rows.forEach(function(row) {
                var name = row.querySelector('td').textContent.toLowerCase();
                if (name.includes(input)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        document.querySelectorAll('.view-details').forEach(function(button) {
            button.addEventListener('click', function() {
                var customer = JSON.parse(this.getAttribute('data-customer'));
                document.getElementById('modalCustomerName').textContent = customer.name;
                document.getElementById('modalCustomerEmail').textContent = customer.email;
                document.getElementById('modalCustomerPhone').textContent = customer.phone;
                document.getElementById('modalCustomerImage').src = '../../uploads/users/profile/' + customer.profile_image;
                document.getElementById('modalCustomerCreatedAt').textContent = customer.created_at;
                $('#customerModal').modal('show');
            });
        });

        document.querySelectorAll('.delete-customer').forEach(function(button) {
            button.addEventListener('click', function() {
                var customerId = this.getAttribute('data-customer-id');
                if (confirm("Are you sure you want to delete this customer?")) {
                    fetch('../../controllers/AdminController.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'deleteCustomer',
                            customerId: customerId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        var alertContainer = document.getElementById('alertContainer');
                        if (data.success) {
                            alertContainer.innerHTML = '<div class="alert alert-success" role="alert">Customer deleted successfully.</div>';
                            setTimeout(function() {
                                location.reload();
                            }, 2000);
                        } else {
                            alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">Error: ' + data.error + '</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        var alertContainer = document.getElementById('alertContainer');
                        alertContainer.innerHTML = '<div class="alert alert-danger" role="alert">An error occurred while deleting the customer.</div>';
                    });
                }
            });
        });
    </script>
</body>
</html>