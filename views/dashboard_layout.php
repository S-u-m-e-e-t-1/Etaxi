<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="icon" href="../assets/images/logo.ico" type="image/x-icon">
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css" rel="stylesheet">

  <style>
    /* Sidebar Styles */
    .sidenav {
      height: 100%;
      width: 0;
      position: fixed;
      top: 0;
      left: 0;
      overflow-x: hidden;
      transition: 0.3s;
      padding-top: 60px;
      z-index: 1000; /* Ensure the sidebar is above the main content */
    }

    .light-mode .sidenav {
      background-color: white;
      color: darkblue;
    }

    .dark-mode .sidenav {
      background-color: #111;
      color: white;
    }

    .sidenav a {
      padding: 10px 20px;
      text-decoration: none;
      font-size: 20px;
      display: block;
      transition: 0.3s;
    }

    .light-mode .sidenav a {
      color: darkblue;
    }

    .dark-mode .sidenav a {
      color: #818181;
    }

    .sidenav a:hover {
      background: #444;
      color: #f1f1f1;
      border-left: 5px solid #00c3ff;
    }

    .sidenav .closebtn {
      position: absolute;
      top: 0;
      right: 20px;
      font-size: 30px;
      cursor: pointer;
    }

    #main {
      transition: margin-left 0.3s;
      padding: 20px;
    }

    /* Theme Styles */
    body.light-mode, .light-mode #contentFrame {
      background-color: white;
      color: darkblue;
    }

    body.dark-mode, .dark-mode #contentFrame {
      background-color: black;
      color: white;
    }

    /* Iframe Styles */
    #contentFrame {
      width: 100%;
      height: calc(100vh - 40px);
      border: none;
    }

    /* Navbar Styling */
    .light-mode .navbar {
      background-color: #f8f9fa !important;
      color: darkblue;
    }

    .light-mode .navbar .nav-link, .light-mode .navbar .navbar-brand {
      color: darkblue !important;
    }

    .dark-mode .navbar {
      background-color: #222 !important;
      color: white;
    }

    .dark-mode .navbar .nav-link, .dark-mode .navbar .navbar-brand {
      color: white !important;
    }

    /* Welcome Popup Styles */
    .welcome-popup {
      display: none;
      position: fixed;
      top: 20px;
      right: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      padding: 15px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1050;
    }
  </style>
</head>

<body class="light-mode">
  <!-- Welcome Popup -->
  <div id="welcomePopup" class="welcome-popup">
    <h4>Welcome <?php echo htmlspecialchars($adminData['name']); ?></h4>
    <p>Managing your application made easy.</p>
  </div>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
      <button id="openBtn" class="btn btn-outline-primary me-2" onclick="openNav()">
        <i class="bi bi-list"></i>
      </button>
      <a class="navbar-brand mx-auto" href="#"><?php echo htmlspecialchars($adminData['name']); ?> Dashboard</a>
      <div class="d-flex align-items-center">
        <a class="nav-link text-white ms-4" href="#" data-link="messages.php"><i class="bi bi-envelope"></i></a>
        <a class="nav-link text-white ms-4" href="#" data-link="notifications.php"><i class="bi bi-bell"></i></a>
        <div class="dropdown">
          <a class="nav-link dropdown-toggle text-white ms-4" href="#" id="profileDropdown" role="button" data-toggle="dropdown">
            <img src="<?php echo htmlspecialchars($adminImage); ?>" alt="Profile" class="rounded-circle" width="30" height="30">
            
          </a>
          <ul class="dropdown-menu dropdown-menu-right">
            <li><a class="dropdown-item" href="#" data-link="edit-profile.php">Edit Profile</a></li>
            <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
          </ul>
        </div>
        <button id="theme-toggle" class="btn btn-outline-primary ms-2">
          <i id="theme-icon" class="bi bi-moon"></i>
        </button>
        <button id="fullscreen-toggle" class="btn btn-outline-light ms-2">
          <i class="bi bi-arrows-fullscreen"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <div id="mySidenav" class="sidenav">
    <span class="closebtn" onclick="closeNav()">&times;</span>
    <?php
    foreach ($options as $item) {
      if (empty($item['subItems'])) {
        echo '<a href="#" data-link="' . $item['link'] . '">' . $item['name'] . '</a>';
      } else {
        $submenuId = "submenu-" . preg_replace('/\s+/', '-', strtolower($item['name'])); // Unique ID for submenu
        echo '<a href="#' . $submenuId . '" class="dropdown-toggle" data-toggle="collapse">' . $item['name'] . '</a>';
        echo '<div id="' . $submenuId . '" class="collapse pl-3">';
        foreach ($item['subItems'] as $subItem) {
          echo '<a class="dropdown-item text-light bg-dark" href="#" data-link="' . $subItem['link'] . '">' . $subItem['name'] . '</a>';
        }
        echo '</div>';
      }
    }
    ?>
  </div>

  <!-- Main Content -->
  <div id="main">
    <div class="container-fluid p-0">
      <iframe id="contentFrame" src="home.php"></iframe>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    // Sidebar Toggle
    function openNav() {
      document.getElementById("mySidenav").style.width = "250px";
    }

    function closeNav() {
      document.getElementById("mySidenav").style.width = "0";
    }

    // Welcome Popup - Disappear after 5 seconds
    window.onload = function() {
      let popup = document.getElementById("welcomePopup");
      popup.style.display = "block";
      setTimeout(() => { popup.style.display = "none"; }, 5000);

      // Add event listeners to all links
      const links = document.querySelectorAll('a[data-link]');
      links.forEach(link => {
        link.addEventListener('click', function(event) {
          event.preventDefault();
          const url = this.getAttribute('data-link');
          const iframe = document.getElementById('contentFrame');
          if (iframe.src.endsWith(url)) {
            iframe.contentWindow.location.reload();
          } else {
            loadContent(url);
          }
        });
      });
    };

    // Theme Toggle
    const themeToggleBtn = document.getElementById("theme-toggle");
    const themeIcon = document.getElementById("theme-icon");
    const body = document.body;
    const iframe = document.getElementById("contentFrame");

    function applyThemeToIframe() {
      if (iframe.contentDocument) {
        let doc = iframe.contentDocument;
        let iframeBody = doc.body;
        if (body.classList.contains("dark-mode")) {
          iframeBody.classList.add("dark-mode");
          iframeBody.classList.remove("light-mode");
        } else {
          iframeBody.classList.add("light-mode");
          iframeBody.classList.remove("dark-mode");
        }
      }
    }

    if (localStorage.getItem("theme") === "dark") {
      body.classList.replace("light-mode", "dark-mode");
      themeIcon.classList.replace("bi-moon", "bi-sun");
    }

    themeToggleBtn.addEventListener("click", () => {
      if (body.classList.contains("light-mode")) {
        body.classList.replace("light-mode", "dark-mode");
        themeIcon.classList.replace("bi-moon", "bi-sun");
        localStorage.setItem("theme", "dark");
      } else {
        body.classList.replace("dark-mode", "light-mode");
        themeIcon.classList.replace("bi-sun", "bi-moon");
        localStorage.setItem("theme", "light");
      }
      applyThemeToIframe();
    });

    // Fullscreen Toggle
    document.getElementById("fullscreen-toggle").addEventListener("click", () => {
      if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
      } else {
        document.exitFullscreen();
      }
    });

    // Load Content in Iframe
    function loadContent(url) {
      document.getElementById('contentFrame').src = url;
    }

    iframe.onload = function() {
      applyThemeToIframe();
    };
  </script>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
