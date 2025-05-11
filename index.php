<?php include 'includes/header.php'; ?>

<div class="container col-xxl-8 px-4 py-5">
    <div class="row flex-lg-row-reverse align-items-center g-5 py-5">
      <div class="col-10 col-sm-8 col-lg-6">
        <div id="carouselExampleCaptions" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide-to="2" aria-label="Slide 3"></button>
          </div>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <img src="assets/images/image1.jpg" class="d-block w-100" alt="Taxi Service">
              <div class="carousel-caption d-none d-md-block">
                <h5>Professional Taxi Service</h5>
                <p>Experience comfortable and reliable transportation with our professional drivers.</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="assets/images/image2.jpg" class="d-block w-100" alt="Modern Fleet">
              <div class="carousel-caption d-none d-md-block">
                <h5>Modern Fleet</h5>
                <p>Our fleet of modern vehicles ensures a safe and comfortable journey.</p>
              </div>
            </div>
            <div class="carousel-item">
              <img src="assets/images/image3.jpg" class="d-block w-100" alt="24/7 Service">
              <div class="carousel-caption d-none d-md-block">
                <h5>24/7 Availability</h5>
                <p>Available round the clock for your transportation needs.</p>
              </div>
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleCaptions" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
      <div class="col-lg-6">
        <h1 class="display-5 fw-bold lh-1 mb-3">Your Trusted Taxi Service Partner</h1>
        <p class="lead">Welcome to ETaxi, where reliability meets comfort. We provide professional taxi services with experienced drivers, modern vehicles, and competitive rates. Whether you need airport transfers, city tours, or corporate travel solutions, we've got you covered 24/7.</p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
          <a href="about.php" class="btn btn-primary btn-lg px-4 me-md-2">About Us</a>
          <a href="#services" class="btn btn-outline-secondary btn-lg px-4">Our Services</a>
        </div>
      </div>
    </div>
  </div>
<div class="container px-4 py-5">
    <h2 class="pb-2 border-bottom">Why Choose Our Taxi Service</h2>

    <div class="row row-cols-1 row-cols-sm-2 g-4">
        <div class="col d-flex flex-column gap-2 text-center align-items-center justify-content-center">
            <div class="feature-icon-small d-flex align-items-center justify-content-center text-bg-primary bg-gradient"
                style="width: 60px; height: 60px; border-radius: 10px;">
                <i class="bi bi-clock-history flex-shrink-0" style="font-size: 28px; color: white;"></i>
            </div>
            <h4 class="fw-semibold mb-0 text-body-emphasis">24/7 Availability</h4>
            <p class="text-body-secondary">Round-the-clock service ensuring you can get a ride whenever you need it, day or night.</p>
        </div>

        <div class="col d-flex flex-column gap-2 text-center align-items-center justify-content-center">
            <div class="feature-icon-small d-flex align-items-center justify-content-center text-bg-primary bg-gradient"
                style="width: 60px; height: 60px; border-radius: 10px;">
                <i class="bi bi-shield-check flex-shrink-0" style="font-size: 28px; color: white;"></i>
            </div>
            <h4 class="fw-semibold mb-0 text-body-emphasis">Safe & Secure</h4>
            <p class="text-body-secondary">Professional licensed drivers, GPS tracking, and secure payment options for your peace of mind.</p>
        </div>

        <div class="col d-flex flex-column gap-2 text-center align-items-center justify-content-center">
            <div class="feature-icon-small d-flex align-items-center justify-content-center text-bg-primary bg-gradient"
                style="width: 60px; height: 60px; border-radius: 10px;">
                <i class="bi bi-cash-coin flex-shrink-0" style="font-size: 28px; color: white;"></i>
            </div>
            <h4 class="fw-semibold mb-0 text-body-emphasis">Competitive Rates</h4>
            <p class="text-body-secondary">Transparent pricing with no hidden fees. Get the best value for your money with our fair fare system.</p>
        </div>

        <div class="col d-flex flex-column gap-2 text-center align-items-center justify-content-center">
            <div class="feature-icon-small d-flex align-items-center justify-content-center text-bg-primary bg-gradient"
                style="width: 60px; height: 60px; border-radius: 10px;">
                <i class="bi bi-car-front-fill flex-shrink-0" style="font-size: 28px; color: white;"></i>
            </div>
            <h4 class="fw-semibold mb-0 text-body-emphasis">Modern Fleet</h4>
            <p class="text-body-secondary">Well-maintained, comfortable vehicles equipped with modern amenities for a pleasant journey.</p>
        </div>
    </div>
</div>

<div class="container px-4 py-5" id="custom-cards">
    <h2 class="pb-2 border-bottom">Our Services Gallery</h2>

    <div class="row row-cols-1 row-cols-lg-3 align-items-stretch g-4 py-5">
        <div class="col">
            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg"
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/gallery/im1.jpg'); background-size: cover; background-position: center;">
                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Airport Transfers</h3>
                    <ul class="d-flex list-unstyled mt-auto">
                        <li class="me-auto">
                            <div class="badge bg-primary">Premium Service</div>
                        </li>
                        <li class="d-flex align-items-center me-3">
                            <i class="bi bi-geo-alt" style="font-size: 1em;"></i>
                            <small class="ms-2">Airport</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg"
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/gallery/im17.jpg'); background-size: cover; background-position: center;">
                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">City Tours</h3>
                    <ul class="d-flex list-unstyled mt-auto">
                        <li class="me-auto">
                            <div class="badge bg-success">Popular</div>
                        </li>
                        <li class="d-flex align-items-center me-3">
                            <i class="bi bi-geo-alt" style="font-size: 1em;"></i>
                            <small class="ms-2">City Wide</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="card card-cover h-100 overflow-hidden text-bg-dark rounded-4 shadow-lg"
                style="background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('assets/gallery/im24.jpg'); background-size: cover; background-position: center;">
                <div class="d-flex flex-column h-100 p-5 pb-3 text-white text-shadow-1">
                    <h3 class="pt-5 mt-5 mb-4 display-6 lh-1 fw-bold">Corporate Travel</h3>
                    <ul class="d-flex list-unstyled mt-auto">
                        <li class="me-auto">
                            <div class="badge bg-info">Business Class</div>
                        </li>
                        <li class="d-flex align-items-center me-3">
                            <i class="bi bi-geo-alt" style="font-size: 1em;"></i>
                            <small class="ms-2">Business District</small>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container px-4 py-5">
    <h2 class="pb-2 border-bottom">Latest Blog Posts</h2>
    <?php
    require_once 'controllers/BlogController.php';
    
    if (isset($blogs) && $blogs['success']) {
        echo '<div class="row px-4 py-5">';
        foreach ($blogs['blogs'] as $blog) {
            ?>
            <div class="col-lg-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title h4 fw-normal"><?php echo htmlspecialchars($blog['title']); ?></h2>
                        <p class="card-text"><?php echo substr(htmlspecialchars($blog['content']), 0, 150) . '...'; ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">By <?php echo htmlspecialchars($blog['author']); ?></small>
                            <small class="text-muted"><?php echo date('M d, Y', strtotime($blog['created_at'])); ?></small>
                        </div>
                        <p class="mt-3"><a class="btn btn-primary" href="blog.php?id=<?php echo $blog['id']; ?>">Read More »</a></p>
                    </div>
                </div>
            </div>
            <?php
        }
        echo '</div>';
    } else {
        echo '<div class="alert alert-info">No blog posts available at the moment.</div>';
    }
    ?>
</div>

<?php include 'includes/footer.php'; ?>