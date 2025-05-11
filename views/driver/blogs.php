<?php
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
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Blogs</h1>
        <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#createBlogModal">
            Create Blog
        </button>
        <input type="text" id="searchKeyword" class="form-control mb-3" placeholder="Search blogs..." onkeyup="searchBlogs()">
        
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
        
        <?php if (!empty($blogs['blogs'])): ?>
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
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
        <?php else: ?>
            <p class="text-muted">No blogs found.</p>
        <?php endif; ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('createBlogForm').addEventListener('submit', createBlog);

            function createBlog(event) {
                event.preventDefault();
                const title = document.getElementById('title').value;
                const content = document.getElementById('content').value;
                const author = document.getElementById('author').value;

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

            function searchBlogs() {
                const keyword = document.getElementById('searchKeyword').value.toLowerCase();
                const rows = document.querySelectorAll('#blogsTable tr');
                rows.forEach(row => {
                    const title = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                    const content = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
                    const author = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
                    if (title.includes(keyword) || content.includes(keyword) || author.includes(keyword)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            }

            document.getElementById('searchKeyword').addEventListener('keyup', searchBlogs);
        });
    </script>
</body>
</html>