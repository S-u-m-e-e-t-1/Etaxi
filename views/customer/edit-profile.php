<?php
require_once '../../controllers/CustomerController.php';

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .profile-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
        }
        .profile-image-container {
            position: relative;
            display: inline-block;
        }
        .profile-upload-label {
            cursor: pointer;
            margin-top: 10px;
            display: inline-block;
        }
        .profile-upload-label:hover {
            text-decoration: underline;
        }
    </style>
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('preview-image').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Profile</h2>
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        <form action="../../controllers/CustomerController.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="updateProfile">
            <div class="row">
                <div class="col-md-4 text-center">
                    <div class="profile-image-container">
                        <?php 
                        $profile_image = !empty($customerData['profile_image']) ? 
                            htmlspecialchars($customerData['profile_image']) : 
                            'default-profile.jpg';
                        ?>
                        <img src="../../uploads/users/profile/<?php echo $profile_image; ?>" 
                             alt="Profile Image" class="profile-image mb-3" id="preview-image">
                    </div>
                    <div class="form-group">
                        <label for="profile_image" class="profile-upload-label btn btn-outline-primary">
                            <i class="fas fa-camera"></i> Change Profile Picture
                        </label>
                        <input type="file" id="profile_image" name="profile_image" 
                               class="form-control d-none" accept="image/*" 
                               onchange="previewImage(this);">
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($customerData['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($customerData['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($customerData['phone']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </div>
        </form>
    </div>
    <!-- Add Font Awesome for icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</body>
</html>