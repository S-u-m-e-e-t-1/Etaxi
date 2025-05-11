<?php include 'includes/header.php'; ?>

<section class="vh-80" style="background-color: #508bfc;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card shadow-2-strong" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <h3 class="mb-4">Sign in</h3>

            <form method="POST" action="controllers/AuthController.php" id="loginForm">
              <input type="hidden" name="action" value="login">

              <!-- Role Selection Dropdown -->
              <div class="form-outline mb-4">
                <select class="form-select form-control-lg" name="role" id="roleSelect">
                  <option value="customer">Login as Customer</option>
                  <option value="driver">Login as Driver</option>
                  <option value="admin" selected>Login as Admin</option>
                </select>
              </div>

              <!-- Email Input -->
              <div class="form-outline mb-4" id="emailInput">
                <input type="email" name="email" id="typeEmailX-2" class="form-control form-control-lg" required />
                <label class="form-label" for="typeEmailX-2">Email</label>
                <button class="btn btn-primary btn-sm mt-2" type="button" id="sendOtpButtonEmail" style="display: none;">Send OTP</button>
              </div>

              <!-- Password Input -->
              <div class="form-outline mb-3" id="passwordInput">
                <input type="password" name="password" id="typePasswordX-2" class="form-control form-control-lg" required />
                <label class="form-label" for="typePasswordX-2">Password</label>
              </div>

              <!-- Phone Input for Driver -->
              <div class="form-outline mb-4" id="phoneInput" style="display: none;">
                <input type="text" name="phone" id="typePhoneX-2" class="form-control form-control-lg" />
                <label class="form-label" for="typePhoneX-2">Phone</label>
                <button class="btn btn-primary btn-sm mt-2" type="button" id="sendOtpButtonPhone" style="display: none;">Send OTP</button>
              </div>

              <!-- OTP Input -->
              <div class="form-outline mb-3" id="otpInput" style="display: none;">
                <input type="text" name="otp" id="typeOtpX-2" class="form-control form-control-lg" />
                <label class="form-label" for="typeOtpX-2">OTP</label>
              </div>

              <!-- Forgot Password Link -->
              <div class="mb-3 text-start">
                <a href="forgot_password.php" class="text-muted">Forgot password?</a>
              </div>

              <!-- Remember Me Checkbox -->
              <div class="form-check d-flex justify-content-start mb-4">
                <input class="form-check-input" type="checkbox" value="" id="form1Example3" />
                <label class="form-check-label ms-2" for="form1Example3"> Remember password </label>
              </div>

              <!-- Login Button -->
              <button class="btn btn-primary btn-lg btn-block" type="submit" id="loginButton">Login</button>
              <button class="btn btn-success btn-lg btn-block" type="button" id="verifyAndLoginButton" style="display: none;">Verify & Login</button>
            </form>

            <hr class="my-4">

            <!-- Create Account Link -->
            <div class="mt-3">
              <p>Don't have an account? <a href="user_register.php">Create Account</a></p>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Login Successful</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        You have successfully logged in.
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
        <h5 class="modal-title" id="errorModalLabel">Login Failed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Invalid email or password. Please try again.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
document.getElementById('roleSelect').addEventListener('change', function () {
    const role = this.value;
    document.getElementById('emailInput').style.display = role === 'admin' || role === 'customer' ? 'block' : 'none';
    document.getElementById('passwordInput').style.display = role === 'admin' ? 'block' : 'none';
    document.getElementById('phoneInput').style.display = role === 'driver' ? 'block' : 'none';
    document.getElementById('otpInput').style.display = role !== 'admin' ? 'block' : 'none';
    document.getElementById('loginButton').style.display = role === 'admin' ? 'block' : 'none';
    document.getElementById('sendOtpButtonEmail').style.display = role === 'customer' ? 'block' : 'none';
    document.getElementById('sendOtpButtonPhone').style.display = role === 'driver' ? 'block' : 'none';
    document.getElementById('verifyAndLoginButton').style.display = role !== 'admin' ? 'block' : 'none';
});

document.getElementById('sendOtpButtonEmail').addEventListener('click', function () {
    const email = document.getElementById('typeEmailX-2').value;

    if (email) {
        fetch('api/mail_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ email: email, role: 'customer' })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('OTP sent to your email.');
            } else {
                alert('Failed to send OTP. ' + data.message);
                location.reload();
            }
        });
    } else {
        alert('Please enter a valid email.');
    }
});

document.getElementById('sendOtpButtonPhone').addEventListener('click', function () {
    const phone = document.getElementById('typePhoneX-2').value;

    if (phone) {
        fetch('api/mobile_otp.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ phone: phone })
        }).then(response => response.json()).then(data => {
            if (data.success) {
                alert('OTP sent to your phone.');
            } else {
                alert('Failed to send OTP. ' + data.message);
                location.reload();
            }
        });
    } else {
        alert('Please enter a valid phone number.');
    }
});

document.getElementById('verifyAndLoginButton').addEventListener('click', function () {
    const formData = new FormData(document.getElementById('loginForm'));

    fetch('controllers/AuthController.php', {
        method: 'POST',
        body: formData
    }).then(response => response.json()).then(data => {
        if (data.success) {
            alert('Login Successful');
            window.location.href = data.role === 'admin' ? 'views/admin/dashboard.php' : data.role === 'driver' ? 'views/driver/dashboard.php' : 'views/customer/dashboard.php';
        } else {
            alert('Login Failed: ' + data.error);
            location.reload();
        }
    });
});

document.getElementById('loginForm').addEventListener('submit', function (event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch('controllers/AuthController.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.text()) // Fetch response as text
        .then(text => {
            console.log("Server Response:", text); // Debugging
            try {
                const data = JSON.parse(text); // Parse JSON
                if (data.success) {
                    new bootstrap.Modal(document.getElementById('successModal')).show();
                    setTimeout(() => {
                        let redirectUrl = {
                            admin: 'views/admin/dashboard.php',
                            driver: 'views/driver/dashboard.php',
                            customer: 'views/customer/dashboard.php'
                        }[data.role] || 'index.php';

                        window.location.href = redirectUrl; // Redirect to respective dashboard
                    }, 2000);
                } else {
                    showErrorModal(data.error);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                showErrorModal('An error occurred. Please try again later.');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showErrorModal('An error occurred. Please check your internet connection.');
        });
});

function showErrorModal(message) {
    document.getElementById('errorModal').querySelector('.modal-body').textContent = message;
    new bootstrap.Modal(document.getElementById('errorModal')).show();
}

</script>

<?php include 'includes/footer.php'; ?>
