<?php

require_once __DIR__ . '/../../controllers/AdminController.php';

// Fetch admin details from the session
$adminId = $_SESSION['admin']['id'];
$adminDetails = $admin->getAdminById($adminId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Edit Profile</h1>
        <div class="card shadow p-4">
            <form id="profileForm" action="../../controllers/AdminController.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="updateProfile">
                
                <div class="row">
                    <!-- Details Section -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name:</label>
                            <input type="text" id="name" name="name" class="form-control editable" value="<?php echo $adminDetails['name']; ?>" readonly required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email:</label>
                            <input type="email" id="email" name="email" class="form-control editable" value="<?php echo $adminDetails['email']; ?>" readonly required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label">Password:</label>
                            <input type="password" id="password" name="password" class="form-control editable" placeholder="Enter new password" readonly>
                        </div>
                        
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone:</label>
                            <input type="text" id="phone" name="phone" class="form-control editable" value="<?php echo $adminDetails['phone']; ?>" readonly required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="created_at" class="form-label">Created At:</label>
                            <input type="text" id="created_at" name="created_at" class="form-control" value="<?php echo $adminDetails['created_at']; ?>" readonly>
                        </div>
                    </div>
                    
                    <!-- Image Section -->
                    <div class="col-md-6 text-center">
                        <div class="mb-3">
                            <label for="profile_image" class="form-label">Profile Image:</label>
                            <input type="file" id="profile_image" name="profile_image" class="form-control editable" disabled>
                        </div>
                        <?php if (!empty($adminDetails['profile_image'])): ?>
                            <img src="../../assets/images/<?php echo $adminDetails['profile_image']; ?>" alt="Profile Image" class="img-thumbnail rounded-circle mt-2" width="300" height="300">
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Button Group -->
                <div id="buttonGroup">
                    <button type="button" id="editBtn" class="btn btn-secondary btn-sm">Edit</button>
                    <div id="editActions" style="display: none;">
                        <button type="submit" class="btn btn-primary btn-sm mt-2">Update Profile</button>
                        <button type="button" id="cancelBtn" class="btn btn-danger btn-sm mt-2">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Enable editing for fields with the "editable" class
        function enableEdit() {
            document.querySelectorAll('.editable').forEach(function(field) {
                if(field.type === 'file'){
                    field.disabled = false;
                } else {
                    field.removeAttribute('readonly');
                }
            });
            document.getElementById('editBtn').style.display = 'none';
            document.getElementById('editActions').style.display = 'block';
        }

        // Cancel editing - reload the page to restore original state
        function disableEdit() {
            window.location.reload();
        }

        document.getElementById('editBtn').addEventListener('click', enableEdit);
        document.getElementById('cancelBtn').addEventListener('click', disableEdit);
    </script>
</body>
</html>