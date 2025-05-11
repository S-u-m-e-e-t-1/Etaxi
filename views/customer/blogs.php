<?php
session_start();
if (!isset($_SESSION['customer'])) {
    header('Location: ../../login.php');
    exit;
}

require_once '../../controllers/BlogController.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blogs</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tbody {
            display: block;
            height: 400px;
            overflow-y: auto;
        }
        thead, tbody tr {
            display: table;
            width: 100%;
            table-layout: fixed;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Blogs</h1>
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#createBlogModal">
            Create Blog
        </button>
        
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
                                <textarea class="form-control" id="content" placeholder="Content" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="author">Author</label>
                                <input type="text" class="form-control" id="author" placeholder="Author" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Create Blog</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scrollable Table -->
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table mt-3">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Author</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody id="blogsTable">
                    <?php foreach ($blogs['blogs'] as $blog): ?>
                        <tr id="blog-<?php echo htmlspecialchars($blog['id']); ?>">
                            <td><?php echo htmlspecialchars($blog['id']); ?></td>
                            <td><?php echo htmlspecialchars($blog['title']); ?></td>
                            <td><?php echo htmlspecialchars($blog['content']); ?></td>
                            <td><?php echo htmlspecialchars($blog['author']); ?></td>
                            <td><?php echo htmlspecialchars($blog['created_at']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('createBlogForm').addEventListener('submit', createBlog);

            function createBlog(event) {
                event.preventDefault();
                const title = document.getElementById('title').value.trim();
                const content = document.getElementById('content').value.trim();
                const author = document.getElementById('author').value.trim();

                if (!title || !content || !author) {
                    alert('Please fill in all fields');
                    return;
                }

                fetch('../../controllers/BlogController.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'createBlog',
                        title: title,
                        content: content,
                        author: author
                    })
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
            }

          

            function loadAllBlogs() {
                fetch('../../controllers/BlogController.php?action=all') // Fetch all blogs
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            renderBlogs(data.blogs);
                        } else {
                            alert('Failed to load blogs');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while loading blogs');
                    });
            }

            function renderBlogs(blogs) {
                const blogsTable = document.getElementById('blogsTable');
                blogsTable.innerHTML = '';

                blogs.forEach(blog => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${blog.id}</td>
                        <td>${blog.title}</td>
                        <td>${blog.content}</td>
                        <td>${blog.author}</td>
                        <td>${blog.created_at}</td>
                    `;
                    blogsTable.appendChild(row);
                });
            }

            // Load all blogs on page load
            loadAllBlogs();
        });
    </script>
</body>
</html>