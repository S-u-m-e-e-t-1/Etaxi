<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>E-Taxi</title>
  <link rel="icon" href="assets/images/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-light text-dark">
  <header>
    <div class="px-3 py-2 text-bg-dark border-bottom">
      <div class="container">
        <div class="d-flex flex-wrap align-items-center justify-content-between">
          <a href="/" class="d-flex align-items-center my-2 my-lg-0 me-lg-auto text-white text-decoration-none">
          <img src="assets/images/logo.png" alt="ETaxi Logo" style="height: 40px; margin-right: 10px;">
          
          </a>

          <!-- Replace the existing login/signup and user section with this -->
          <div class="d-flex align-items-center gap-3">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="btn btn-outline-light">
              <i id="theme-icon" class="bi bi-moon"></i>
            </button>

            <?php if (!isset($_SESSION['role']) || empty($_SESSION['role'])): ?>
              <div class="d-flex align-items-center">
                <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                <a href="user_register.php" class="btn btn-outline-light">Sign Up</a>
              </div>
            <?php else: ?>
              <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center gap-2" 
                        type="button" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                  <i class="bi bi-person-circle"></i>
                  <span><?php echo isset($_SESSION['role']) ? ucfirst($_SESSION['role']) : 'User'; ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">
                  <?php
                  switch($_SESSION['role']) {
                      case 'admin':
                          echo '<li><a class="dropdown-item" href="views/admin/dashboard.php"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</a></li>';
                          break;
                      case 'driver':
                          echo '<li><a class="dropdown-item" href="views/driver/dashboard.php"><i class="bi bi-car-front me-2"></i>Driver Dashboard</a></li>';
                          break;
                      case 'customer':
                          echo '<li><a class="dropdown-item" href="views/customer/dashboard.php"><i class="bi bi-person me-2"></i>Customer Dashboard</a></li>';
                          break;
                      default:
                          echo '<li><a class="dropdown-item" href="index.php"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>';
                  }
                  ?>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="views/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                </ul>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="container">
      <header class="d-flex justify-content-center py-3">
        <ul class="nav nav-pills">
          <li class="nav-item"><a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Home</a></li>
          <li class="nav-item"><a href="earn.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'earn.php' ? 'active' : ''; ?>">Earn</a></li>
          <li class="nav-item"><a href="about.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>">About</a></li>
          <li class="nav-item"><a href="help.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'help.php' ? 'active' : ''; ?>">Help</a></li>
          <li class="nav-item"><a href="gallery.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>">Gallery</a></li>
          <li class="nav-item"><a href="blog.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : ''; ?>">Blog</a></li>
          <li class="nav-item"><a href="game/" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'games.php' ? 'active' : ''; ?>">Games</a></li>
        </ul>
      </header>
    </div>
  </header>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const currentLocation = window.location.pathname.split("/").pop();
      const navLinks = document.querySelectorAll('.nav-link');

      // Remove 'active' class from all links
      navLinks.forEach(link => link.classList.remove('active'));

      // Add 'active' class to the matching link
      navLinks.forEach(link => {
        if (link.getAttribute('href') === currentLocation) {
          link.classList.add('active');
        }
      });

      // Add click event to change the active class
      navLinks.forEach(link => {
        link.addEventListener('click', function () {
          navLinks.forEach(l => l.classList.remove('active'));
          this.classList.add('active');
        });
      });

      // Theme Toggle Functionality
      const themeToggleBtn = document.getElementById("theme-toggle");
      const themeIcon = document.getElementById("theme-icon");
      const body = document.body;

      // Check local storage for saved theme
      if (localStorage.getItem("theme") === "dark") {
        body.classList.replace("bg-light", "bg-dark");
        body.classList.replace("text-dark", "text-light");
        themeIcon.classList.replace("bi-moon", "bi-sun");
      }

      themeToggleBtn.addEventListener("click", () => {
        if (body.classList.contains("bg-light")) {
          body.classList.replace("bg-light", "bg-dark");
          body.classList.replace("text-dark", "text-light");
          themeIcon.classList.replace("bi-moon", "bi-sun");
          localStorage.setItem("theme", "dark");
        } else {
          body.classList.replace("bg-dark", "bg-light");
          body.classList.replace("text-light", "text-dark");
          themeIcon.classList.replace("bi-sun", "bi-moon");
          localStorage.setItem("theme", "light");
        }
      });
    });
  </script>


