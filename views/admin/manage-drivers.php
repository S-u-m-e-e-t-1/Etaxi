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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Drivers</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        .scrollable-table {
            max-height: 400px;
            overflow-y: auto;
        }
        .sortable {
            cursor: pointer;
        }
        .sortable:after {
            content: '↕';
            margin-left: 5px;
            opacity: 0.5;
        }
        .sortable.asc:after {
            content: '↑';
            opacity: 1;
        }
        .sortable.desc:after {
            content: '↓';
            opacity: 1;
        }
        .toast-top-right {
            top: 65px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Manage Drivers</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php else: ?>
            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="text" id="searchApproved" class="form-control" placeholder="Search Approved Drivers by ID or Name">
                </div>
                <div class="col-md-6">
                    <input type="text" id="searchPending" class="form-control" placeholder="Search Pending Drivers by ID or Name">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h2 class="mt-4">Approved Drivers</h2>
                    <div class="scrollable-table">
                        <table class="table table-bordered" id="approvedDriversTable">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sl_no = 1; ?>
                                <?php foreach ($drivers as $driver): ?>
                                    <?php if ($driver['status'] === 'approved'): ?>
                                        <tr>
                                            <td><?php echo $sl_no++; ?></td>
                                            <td><img src="../../uploads/drivers/profile/<?php echo $driver['profile_image']; ?>" alt="Profile Image" width="50"></td>
                                            <td><?php echo $driver['name']; ?></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="viewDetails(<?php echo $driver['id']; ?>)">View Details</button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <h2 class="mt-4">Pending Drivers</h2>
                    <div class="scrollable-table">
                        <table class="table table-bordered" id="pendingDriversTable">
                            <thead>
                                <tr>
                                    <th>Sl No</th>
                                    <th>Profile</th>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $sl_no = 1; ?>
                                <?php foreach ($drivers as $driver): ?>
                                    <?php if ($driver['status'] === 'pending'): ?>
                                        <tr>
                                            <td><?php echo $sl_no++; ?></td>
                                            <td><img src="../../uploads/drivers/profile/<?php echo $driver['profile_image']; ?>" alt="Profile Image" width="50"></td>
                                            <td><?php echo $driver['name']; ?></td>
                                            <td>
                                                <button class="btn btn-primary" onclick="viewDetails(<?php echo $driver['id']; ?>)">View Details</button>
                                                <button class="btn btn-success" onclick="approveDriver(<?php echo $driver['id']; ?>)">Approve</button>
                                                <button class="btn btn-danger" onclick="rejectDriver(<?php echo $driver['id']; ?>)">Reject</button>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Popup Modal for Viewing Details -->
        <div class="modal fade" id="detailsModal" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="detailsModalLabel">Driver & Vehicle Details</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row" id="driverDetails">
                            <!-- Content will be loaded here via AJAX -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="updateButton" onclick="enableUpdate()">Update</button>
                        <button type="button" class="btn btn-danger" id="deleteButton" onclick="deleteDriver()">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Confirmation Modal -->
        <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Action</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p id="confirmationMessage"></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmActionBtn">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

  <script>
    // Configuration
    let sortColumn = 'name';
    let sortDirection = 'asc';

    // Toast configuration
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 3000
    };

    // Input validation rules
    const validationRules = {
        name: /^[a-zA-Z\s]{2,50}$/,
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        phone: /^\+?[\d\s-]{10,15}$/,
        id_number: /^[\w-]{5,20}$/
    };

    function validateInput(field, value) {
        if (!validationRules[field].test(value)) {
            throw new Error(`Invalid ${field} format`);
        }
        return true;
    }

    function showConfirmation(message, callback) {
        $('#confirmationMessage').text(message);
        $('#confirmActionBtn').off('click').on('click', callback);
        $('#confirmationModal').modal('show');
    }

    function handleAjaxError(xhr, status, error) {
        toastr.error(xhr.responseText || 'An error occurred while processing your request');
    }

    function sortTable(table, column) {
        const rows = Array.from(table.find('tbody tr'));
        const index = table.find('th').index(column);
        const isAsc = column.hasClass('asc');
        
        rows.sort((a, b) => {
            const aValue = $(a).find('td').eq(index).text();
            const bValue = $(b).find('td').eq(index).text();
            return isAsc ? bValue.localeCompare(aValue) : aValue.localeCompare(bValue);
        });
        
        table.find('tbody').empty().append(rows);
        table.find('th').removeClass('asc desc');
        column.addClass(isAsc ? 'desc' : 'asc');
    }

    function viewDetails(driverId) {
        $.ajax({
            url: '../../controllers/AdminController.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ action: 'viewDetails', driverId: driverId }),
            success: function(response) {
                try {
                    const data = JSON.parse(response);
                    const driver = data.driver;
                    const vehicle = data.vehicle;
                    
                    let driverHtml = `
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Driver Information</h5>
                                </div>
                                <div class="card-body">
                                    <form>
                                        <input type="hidden" id="driverId" value="${driver.id}">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" id="driverName" value="${driver.name}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Email</label>
                                            <input type="email" id="driverEmail" value="${driver.email}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="tel" id="driverPhone" value="${driver.phone}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>ID Number</label>
                                            <input type="text" id="driverIdNumber" value="${driver.id_number}" class="form-control" disabled>
                                        </div>
                                        <div class="form-group">
                                            <label>Status</label>
                                            <input type="text" id="driverStatus" value="${driver.status}" class="form-control" disabled>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>Profile Image</label>
                                                    <div class="mt-2">
                                                        <img src="../../uploads/drivers/profile/${driver.profile_image}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label>ID Image</label>
                                                    <div class="mt-2">
                                                        <img src="../../uploads/drivers/id/${driver.id_image}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>`;

                    let vehicleHtml = '';
                    if (vehicle) {
                        vehicleHtml = `
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Vehicle Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <form>
                                            <div class="form-group">
                                                <label>License Number</label>
                                                <input type="text" id="licenseNumber" value="${vehicle.license_number}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Vehicle Type</label>
                                                <input type="text" id="vehicleType" value="${vehicle.vehicle_type}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Vehicle Model</label>
                                                <input type="text" id="vehicleModel" value="${vehicle.vehicle_model}" class="form-control" disabled>
                                            </div>
                                            <div class="form-group">
                                                <label>Vehicle Number</label>
                                                <input type="text" id="vehicleNumber" value="${vehicle.vehicle_number}" class="form-control" disabled>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>License Image</label>
                                                        <div class="mt-2">
                                                            <img src="../../uploads/drivers/license/${vehicle.license_image}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Vehicle Image</label>
                                                        <div class="mt-2">
                                                            <img src="../../uploads/drivers/vehicle/${vehicle.vehicle_image}" class="img-fluid img-thumbnail" style="max-height: 150px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>`;
                    } else {
                        vehicleHtml = `
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">Vehicle Information</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            No vehicle information available for this driver.
                                            <form>
                                                <div class="form-group mt-3">
                                                    <label>License Number</label>
                                                    <input type="text" id="licenseNumber" class="form-control" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>Vehicle Type</label>
                                                    <input type="text" id="vehicleType" class="form-control" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>Vehicle Model</label>
                                                    <input type="text" id="vehicleModel" class="form-control" disabled>
                                                </div>
                                                <div class="form-group">
                                                    <label>Vehicle Number</label>
                                                    <input type="text" id="vehicleNumber" class="form-control" disabled>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                    }

                    $('#driverDetails').html(driverHtml + vehicleHtml);
                    $('#detailsModal').modal('show');
                } catch (error) {
                    toastr.error('Error parsing driver details');
                    console.error(error);
                }
            },
            error: handleAjaxError
        });
    }

    function approveDriver(driverId) {
        showConfirmation('Are you sure you want to approve this driver?', function() {
            $('#confirmationModal').modal('hide');
            $.ajax({
                url: '../../controllers/AdminController.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ action: 'approve', driverId: driverId }),
                success: function(response) {
                    toastr.success('Driver approved successfully');
                    setTimeout(() => location.reload(), 1000);
                },
                error: handleAjaxError
            });
        });
    }

    function rejectDriver(driverId) {
        showConfirmation('Are you sure you want to reject this driver?', function() {
            $('#confirmationModal').modal('hide');
            $.ajax({
                url: '../../controllers/AdminController.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ action: 'reject', driverId: driverId }),
                success: function(response) {
                    toastr.success('Driver rejected successfully');
                    setTimeout(() => location.reload(), 1000);
                },
                error: handleAjaxError
            });
        });
    }

    function deleteDriver() {
        const driverId = $('#driverId').val();
        showConfirmation('Are you sure you want to delete this driver? This action cannot be undone.', function() {
            $('#confirmationModal').modal('hide');
            $.ajax({
                url: '../../controllers/AdminController.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ action: 'delete', driverId: driverId }),
                success: function(response) {
                    toastr.success('Driver deleted successfully');
                    setTimeout(() => location.reload(), 1000);
                },
                error: handleAjaxError
            });
        });
    }

    function enableUpdate() {
        $('#driverName, #driverEmail, #driverPhone, #driverIdNumber, #driverStatus, #licenseNumber, #vehicleType, #vehicleModel, #vehicleNumber').prop('disabled', false);
        $('#updateButton').text('Save').attr('onclick', 'saveDriver()');
    }

    function saveDriver() {
        const driverId = $('#driverId').val();
        try {
            const driverData = {
                name: $('#driverName').val(),
                email: $('#driverEmail').val(),
                phone: $('#driverPhone').val(),
                id_number: $('#driverIdNumber').val(),
                status: $('#driverStatus').val()
            };

            const vehicleData = {
                license_number: $('#licenseNumber').val(),
                vehicle_type: $('#vehicleType').val(),
                vehicle_model: $('#vehicleModel').val(),
                vehicle_number: $('#vehicleNumber').val()
            };

            // Validate driver inputs
            Object.keys(driverData).forEach(field => {
                if (validationRules[field]) {
                    validateInput(field, driverData[field]);
                }
            });

            // Add vehicle validation rules
            const vehicleValidationRules = {
                license_number: /^[A-Z0-9-]{5,20}$/,
                vehicle_number: /^[A-Z0-9-]{5,15}$/
            };

            // Validate vehicle inputs
            Object.keys(vehicleData).forEach(field => {
                if (vehicleValidationRules[field] && vehicleData[field]) {
                    if (!vehicleValidationRules[field].test(vehicleData[field])) {
                        throw new Error(`Invalid ${field.replace('_', ' ')} format`);
                    }
                }
            });

            $.ajax({
                url: '../../controllers/AdminController.php',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ 
                    action: 'update', 
                    driverId: driverId, 
                    driverData: driverData,
                    vehicleData: vehicleData
                }),
                success: function(response) {
                    toastr.success('Information updated successfully');
                    setTimeout(() => location.reload(), 1000);
                },
                error: handleAjaxError
            });
        } catch (error) {
            toastr.error(error.message);
        }
    }

    $(document).ready(function() {
        // Initialize sorting
        $('.sortable').click(function() {
            sortTable($(this).closest('table'), $(this));
        });

        // Search functionality
        $('#searchApproved, #searchPending').on('keyup', function() {
            const value = $(this).val().toLowerCase();
            const table = $(this).attr('id') === 'searchApproved' ? 
                $('#approvedDriversTable') : $('#pendingDriversTable');
            
            table.find('tbody tr').each(function() {
                const text = $(this).text().toLowerCase();
                $(this).toggle(text.indexOf(value) > -1);
            });
        });
    });
</script>
</body>
</html>