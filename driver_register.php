<?php include 'includes/header.php'; ?>
<section class="h-100 h-custom gradient-custom-2">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12">
        <div class="card card-registration card-registration-2" style="border-radius: 15px;">
          <div class="card-body p-0">
            <div class="row g-0">
              <div class="col-lg-6">
                <div class="p-5">
                  <h3 class="fw-normal mb-5" style="color: #4835d4;">Driver Registration</h3>

                  <form id="registrationForm" action="controllers/AuthController.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="register_driver">
                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="name" name="name" class="form-control form-control-lg" required />
                        <label class="form-label" for="name">Name</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="email" id="email" name="email" class="form-control form-control-lg" required />
                        <label class="form-label" for="email">Email</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="password" id="password" name="password" class="form-control form-control-lg" required />
                        <label class="form-label" for="password">Password</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="phone" name="phone" class="form-control form-control-lg" required />
                        <label class="form-label" for="phone">Phone</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="id_number" name="id_number" class="form-control form-control-lg" required />
                        <label class="form-label" for="id_number">Aadhaar Number</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="file" id="id_image" name="id_image" class="form-control form-control-lg" required />
                        <label class="form-label" for="id_image">Aadhaar Proof</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="file" id="profile_image" name="profile_image" class="form-control form-control-lg" required />
                        <label class="form-label" for="profile_image">Profile Image</label>
                      </div>
                    </div>

                </div>
              </div>

              <div class="col-lg-6">
                <div class="p-5">
                  <h3 class="fw-normal mb-5" style="color: #4835d4;">Vehicle Details</h3>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="vehicle_type" name="vehicle_type" class="form-control form-control-lg" required />
                        <label class="form-label" for="vehicle_type">Vehicle Type</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="vehicle_model" name="vehicle_model" class="form-control form-control-lg" required />
                        <label class="form-label" for="vehicle_model">Vehicle Model</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="vehicle_number" name="vehicle_number" class="form-control form-control-lg" required />
                        <label class="form-label" for="vehicle_number">Vehicle Number</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="file" id="vehicle_image" name="vehicle_image" class="form-control form-control-lg" required />
                        <label class="form-label" for="vehicle_image">Vehicle Image</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="license_number" name="license_number" class="form-control form-control-lg" required />
                        <label class="form-label" for="license_number">License Number</label>
                      </div>
                    </div>

                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="file" id="license_image" name="license_image" class="form-control form-control-lg" required />
                        <label class="form-label" for="license_image">License Proof</label>
                      </div>
                    </div>
                    <div class="mb-4 pb-2">
                      <div data-mdb-input-init class="form-outline">
                        <input type="text" id="otp" name="otp" class="form-control form-control-lg" required />
                        <label class="form-label" for="otp">OTP</label>
                      </div>
                    </div>
                    <button type="button" onclick="sendOTP()" class="btn btn-primary btn-lg">Send OTP</button>
                    <button type="button" onclick="verifyOTP()" class="btn btn-secondary btn-lg">Verify OTP & Submit</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include 'includes/footer.php'; ?>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        OTP verified successfully! and Registration successful!
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="errorMessage">
        <!-- Error message will be inserted here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  document.getElementById("registrationForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission

    const email = document.getElementById('email').value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/; // Regex to validate email format

    // Validate email format
    if (!emailRegex.test(email)) {
        document.getElementById("errorMessage").innerText = "Please enter a valid email address.";
        let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
        return;
    }

    const enteredOtp = document.getElementById('otp').value;
    const phone = document.getElementById('phone').value;

    // Verify OTP
    fetch('api/verify_mobile_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `otp=${enteredOtp}&phone=${phone}`
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            document.getElementById("errorMessage").innerText = "Invalid OTP. Please try again.";
            let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
            return;
        }

        // If OTP is valid, submit the form
        const formData = new FormData(document.getElementById('registrationForm'));
        fetch('controllers/AuthController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let successModal = new bootstrap.Modal(document.getElementById('successModal'));
                successModal.show();
                setTimeout(() => { window.location.href = "login.php"; }, 2000);
            } else {
                document.getElementById("errorMessage").innerText = data.error || "Registration failed. Please try again.";
                let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
                errorModal.show();
            }
        })
        .catch(error => {
            document.getElementById("errorMessage").innerText = "Something went wrong!";
            let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
            errorModal.show();
        });
    })
    .catch(error => {
        document.getElementById("errorMessage").innerText = "Error verifying OTP. Please try again.";
        let errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        errorModal.show();
    });
});

  function sendOTP() {
    const phone = document.getElementById('phone').value;

    if (!phone) {
        alert('Please enter your phone number.');
        return;
    }

    fetch('api/mobile_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `phone=${phone}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('OTP sent to your phone.');
        } else {
            alert('Failed to send OTP. Please try again.');
        }
    })
    .catch(error => {
        alert('Error sending OTP. Please try again.');
    });
  }

  function verifyOTP() {
    const enteredOtp = document.getElementById('otp').value;
    const phone = document.getElementById('phone').value;

    if (!enteredOtp || !phone) {
        alert('Please enter your phone number and OTP.');
        return;
    }

    fetch('api/verify_mobile_otp.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `otp=${enteredOtp}&phone=${phone}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('OTP verified successfully!');
        } else {
            alert('Invalid OTP. Please try again.');
        }
    })
    .catch(error => {
        alert('Error verifying OTP. Please try again.');
    });
  }
</script>