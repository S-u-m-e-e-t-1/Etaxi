<?php include 'includes/header.php'; ?>

<?php
// Add this PHP code at the top to get all images
$gallery_path = 'assets/gallery/';
$images = glob($gallery_path . "*.{jpg,jpeg,png,gif}", GLOB_BRACE);
$first_image = !empty($images) ? $images[0] : '';
?>

<div class="gallery-container">
    <!-- Main large image display -->
    <div class="main-image">
        <img src="<?php echo $first_image; ?>" id="featured-image" alt="Featured Image">
    </div>

    <!-- Horizontal slider -->
    <div class="horizontal-slider">
        <div class="slider-track">
            <?php foreach($images as $image): ?>
            <div class="slide">
                <img src="<?php echo $image; ?>" onclick="changeImage(this.src)" alt="Gallery Image">
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Vertical slider -->
    <div class="vertical-slider">
        <div class="slider-track-vertical">
            <?php foreach($images as $image): ?>
            <div class="slide">
                <img src="<?php echo $image; ?>" onclick="changeImage(this.src)" alt="Gallery Image">
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<style>
.gallery-container {
    display: grid;
    grid-template-columns: 100px 1fr;
    grid-gap: 20px;
    padding: 20px;
}

.main-image {
    grid-column: 2;
    grid-row: 1 / span 2;
}

.main-image img {
    width: 100%;
    height: auto;
    max-height: 600px;
    object-fit: contain;
}

.horizontal-slider {
    grid-column: 1 / span 2;
    grid-row: 3;
    overflow: hidden;
    height: 150px;
}

.vertical-slider {
    grid-column: 1;
    grid-row: 1 / span 2;
    overflow: hidden;
    width: 100px;
    height: 400px;
}

.slider-track {
    display: flex;
    animation: slideHorizontal 15s linear infinite;
}

.slider-track-vertical {
    display: flex;
    flex-direction: column;
    animation: slideVertical 15s linear infinite;
}

.slide {
    flex: 0 0 auto;
    margin: 5px;
}

.slide img {
    width: 90px;
    height: 90px;
    object-fit: cover;
    cursor: pointer;
}

/* Add new styles for horizontal slider images */
.horizontal-slider .slide img {
    width: 140px;
    height: 140px;
}

@keyframes slideHorizontal {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@keyframes slideVertical {
    0% { transform: translateY(0); }
    100% { transform: translateY(-50%); }
}
</style>

<script>
function changeImage(src) {
    document.getElementById('featured-image').src = src;
}

// Modified script with auto-update for vertical slider
document.addEventListener('DOMContentLoaded', function() {
    const hTrack = document.querySelector('.slider-track');
    const vTrack = document.querySelector('.slider-track-vertical');
    
    const hClones = hTrack.innerHTML;
    const vClones = vTrack.innerHTML;
    
    hTrack.innerHTML += hClones;
    vTrack.innerHTML += vClones;

    // Auto update vertical slider every 3 seconds
    const verticalImages = document.querySelectorAll('.slider-track-vertical .slide img');
    let currentIndex = 0;

    setInterval(() => {
        currentIndex = (currentIndex + 1) % verticalImages.length;
        const nextImage = verticalImages[currentIndex].src;
        changeImage(nextImage);
    }, 3000);
});
</script>

<?php include 'includes/footer.php'; ?>