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
    <title>Manage Blogs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Manage Blogs</h1>
            <button class="btn btn-primary" data-toggle="modal" data-target="#createBlogModal">
                Create Blog
            </button>
        </div>
        
        <input type="text" id="search" class="form-control mb-3" placeholder="Search by title or author" onkeyup="searchBlogs()">
        
        <div class="row" id="blogs-container">
            <?php foreach ($blogs as $blog): ?>
                <div class="col-md-4 mb-3 blog-card" data-title="<?= $blog['title'] ?>" data-author="<?= $blog['author'] ?>">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?= $blog['title'] ?></h5>
                            <p class="card-text">by <?= $blog['author'] ?></p>
                            <button class="btn btn-primary" onclick="viewBlog(<?= $blog['id'] ?>)">View</button>
                            <button class="btn btn-danger" onclick="deleteBlog(<?= $blog['id'] ?>)">Delete</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Create Blog Modal -->
    <div class="modal fade" id="createBlogModal" tabindex="-1" role="dialog" aria-labelledby="createBlogModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createBlogModalLabel">Create Blog</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="createBlogForm">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" placeholder="Title" required>
                        </div>
                        <div class="form-group">
                            <label for="content">Content</label>
                            <textarea class="form-control" id="content" placeholder="Content" rows="6" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" id="author" value="<?= $_SESSION['admin']['name'] ?>" readonly>
                        </div>
                        <button type="submit" class="btn btn-primary">Create Blog</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Blog Modal -->
    <div class="modal fade" id="blogModal" tabindex="-1" aria-labelledby="blogModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="blogModalLabel">Blog Details</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5 id="blogTitle"></h5>
                    <p id="blogAuthor"></p>
                    <p id="blogContent"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        function searchBlogs() {
            const searchInput = document.getElementById('search').value.toLowerCase();
            const blogCards = document.querySelectorAll('.blog-card');

            blogCards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const author = card.getAttribute('data-author').toLowerCase();

                if (title.includes(searchInput) || author.includes(searchInput)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Create blog form submission
        document.getElementById('createBlogForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                action: 'createBlog',
                title: document.getElementById('title').value,
                content: document.getElementById('content').value,
                author: document.getElementById('author').value
            };

            fetch('../../controllers/BlogController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Blog created successfully!');
                    $('#createBlogModal').modal('hide');
                    location.reload();
                } else {
                    alert('Failed to create blog: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the blog');
            });
        });

        // View blog
        function viewBlog(id) {
            fetch('../../controllers/AdminController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'viewBlog', blogId: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data) {
                    document.getElementById('blogTitle').innerText = data.title;
                    document.getElementById('blogAuthor').innerText = `by ${data.author}`;
                    document.getElementById('blogContent').innerText = data.content;
                    $('#blogModal').modal('show');
                } else {
                    alert('Failed to fetch blog details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching blog details');
            });
        }

        // Delete blog
        function deleteBlog(id) {
            if (confirm('Are you sure you want to delete this blog?')) {
                fetch('../../controllers/AdminController.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'deleteBlog', blogId: id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete blog');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the blog');
                });
            }
        }
    </script>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>