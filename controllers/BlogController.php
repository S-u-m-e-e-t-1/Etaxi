<?php
require_once __DIR__ . '/../models/Blog.php';
require_once __DIR__ . '/../includes/database.php';


$database = new Database();
$db = $database->getConnection();
$blog = new Blog($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure content type is set
    if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], "application/json") !== false) {
        $_POST = json_decode(file_get_contents("php://input"), true);
    }

    $action = $_POST['action'] ?? '';

    if ($action === 'createBlog') {
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $author = trim($_POST['author'] ?? '');

        // Call the createBlog method
        $result = $blog->createBlog($title, $content, $author);

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? 'random'; // Default to random for homepage

    if ($action === 'all') {
        // Fetch all blogs without pagination
        $blogs = $blog->getAllBlogs();
        header('Content-Type: application/json');
        echo json_encode($blogs);
        exit;
    } else {
        $blogs = $blog->getRandomBlogs();
    }
}

?>