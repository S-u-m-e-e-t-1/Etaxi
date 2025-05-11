<?php include 'includes/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5 d-flex justify-content-center">
    <div class="card shadow-lg" style="width: 100%; max-width: 500px;">
        <div class="card-header bg-primary text-white text-center">
            <h2>User Registration</h2>
        </div>
        <div class="card-body">
            <form id="registrationForm" action="controllers/AuthController.php?action=register" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name:</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone:</label>
                    <div class="input-group">
                        <input type="text" id="phone" name="phone" class="form-control" required>
                        <button type="button" id="sendOtpButton" class="btn btn-primary">Send OTP</button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="otp" class="form-label">OTP:</label>
                    <div class="input-group">
                        <input type="text" id="otp" name="otp" class="form-control" required>
                        <button type="button" id="verifyOtpButton" class="btn btn-success">Verify OTP</button>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password:</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                
                <button type="submit" id="registerButton" class="btn btn-primary w-100" disabled>Register</button>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="successModalLabel">Registration Successful</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Your registration was successful! Redirecting to the login page...
            </div>
        </div>
    </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="errorModalLabel">Error</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="errorMessage">
                An error occurred. Please try again.
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let isOtpVerified = false;
    let isEmailValid = false; // Track email validation status

    document.getElementById('sendOtpButton').addEventListener('click', function() {
        const phone = document.getElementById('phone').value;
        if (!phone) {
            alert('Please enter your phone number.');
            return;
        }

        fetch('api/mobile_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ phone })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('OTP sent successfully!');
            } else {
                showErrorModal('Failed to send OTP: ' + data.message);
            }
        })
        .catch(error => showErrorModal('Error: ' + error.message));
    });

    document.getElementById('verifyOtpButton').addEventListener('click', function() {
        const phone = document.getElementById('phone').value;
        const otp = document.getElementById('otp').value;

        if (!phone || !otp) {
            alert('Please enter your phone number and OTP.');
            return;
        }

        fetch('api/verify_mobile_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ phone, otp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('OTP verified successfully!');
                isOtpVerified = true;
                document.getElementById('registerButton').disabled = false;
            } else {
                showErrorModal('Failed to verify OTP: ' + data.message);
                isOtpVerified = false;
                document.getElementById('registerButton').disabled = true;
            }
        })
        .catch(error => showErrorModal('Error: ' + error.message));
    });

    document.getElementById('email').addEventListener('blur', function () {
        const email = this.value;

        if (!email) {
            showErrorModal('Please enter an email address.');
            isEmailValid = false;
            return;
        }

        fetch('api/validate_email_api.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ email })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                showErrorModal(data.message);
                isEmailValid = false;
            } else {
                alert('Email is valid.');
                isEmailValid = true;
            }
        })
        .catch(error => {
            showErrorModal('Error: ' + error.message);
            isEmailValid = false;
        });
    });

    document.getElementById('registerButton').addEventListener('click', function(event) {
        event.preventDefault(); // Prevent default form submission

        const email = document.getElementById('email').value;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Check if email is valid
        if (!emailRegex.test(email)) {
            showErrorModal('Please enter a valid email address.');
            return;
        }

        // Check if email is validated
        if (!isEmailValid) {
            showErrorModal('Please validate your email before submitting.');
            return;
        }

        // Check if OTP is verified
        if (!isOtpVerified) {
            showErrorModal('Please verify your OTP before submitting.');
            return;
        }

        // If all checks pass, submit the form
        const formData = new FormData(document.getElementById('registrationForm'));
        fetch(document.getElementById('registrationForm').action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(() => {
                    window.location.href = 'login.php';
                }, 3000);
            } else {
                showErrorModal(data.error || 'Registration failed. Please try again.');
            }
        })
        .catch(error => showErrorModal('Error: ' + error.message));
    });

    function showErrorModal(message) {
        document.getElementById('errorMessage').textContent = message;
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    }
</script>

<?php include 'includes/footer.php'; ?>
