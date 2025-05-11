<?php
require_once 'controllers/BlogController.php';
include 'includes/header.php'; 
?>

<div class="container">
    <h1 class="mt-4">Blogs</h1>
    <input type="text" id="searchKeyword" class="form-control mt-3" placeholder="Search blogs...">

    <?php 
    $blogs = $blog->getAllBlogs();
    $items_per_page = 6;
    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $total_items = count($blogs['blogs']);
    $total_pages = ceil($total_items / $items_per_page);
    $offset = ($current_page - 1) * $items_per_page;
    $current_blogs = array_slice($blogs['blogs'], $offset, $items_per_page);

    if (!empty($blogs['blogs'])): ?>
        <div class="row mt-4" id="blogsContainer">
            <?php foreach ($current_blogs as $blog): ?>
                <div class="col-md-4 mb-4 blog-card">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center rounded-circle bg-primary text-white mb-3" style="width: 50px; height: 50px; font-size: 24px;">
                                <?php echo strtoupper(substr($blog['author'], 0, 1)); ?>
                            </div>
                            <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                            <p class="card-text text-truncate" style="max-height: 100px; overflow: hidden;"><?php echo htmlspecialchars(substr($blog['content'], 0, 150)) . '...'; ?></p>
                        </div>
                        <div class="card-footer bg-transparent border-0 text-muted small">
                            <div>By <?php echo htmlspecialchars($blog['author']); ?></div>
                            <div><?php echo htmlspecialchars($blog['created_at']); ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <nav aria-label="Blog pagination" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" tabindex="-1">Previous</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php else: ?>
        <p class="text-center mt-4">No blogs found.</p>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchKeyword');
        searchInput.addEventListener('keyup', function() {
            const keyword = searchInput.value.toLowerCase();
            const cards = document.querySelectorAll('.blog-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.card-title').textContent.toLowerCase();
                const content = card.querySelector('.card-text').textContent.toLowerCase();
                const author = card.querySelector('.text-muted').textContent.toLowerCase();
                
                if (title.includes(keyword) || content.includes(keyword) || author.includes(keyword)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
