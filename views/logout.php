<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session data on the server
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Prevent caching
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="refresh" content="2;url=login.php">
  <title>Logging Out...</title>
  <script>
    // Prevent back button from accessing cached pages
    window.history.forward();
    window.onload = function () {
      setTimeout(function () {
        window.location.replace("../login.php");
      }, 2000);
    };
  </script>
</head>
<body>
<div style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); 
            padding: 20px; border: 1px solid #ccc; border-radius: 10px; 
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; 
            background-color: #f9f9f9;">
  <h2>You have been logged out.</h2>
  <p>Redirecting to login page...</p>
</div>
</body>
</html>
