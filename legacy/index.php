<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>TripSync - Modern Bus Transport</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <link href="img/favicon.png" rel="icon">

  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* --- General Styles --- */
    body {
      font-family: 'Open Sans', sans-serif;
      color: #444;
      overflow-x: hidden;
    }

    a {
      text-decoration: none;
      color: #0d6efd;
      transition: 0.3s;
    }

    h1, h2, h3, h4, h5, h6 {
      font-family: 'Montserrat', sans-serif;
    }

    /* --- Top Bar --- */
    #topbar {
      background: #fff;
      border-bottom: 1px solid #eee;
      font-size: 14px;
      padding: 10px 0;
    }

    #topbar .contact-info i {
      color: #0d6efd;
      margin-right: 5px;
    }

    #topbar .contact-info a {
      color: #444;
      margin-right: 20px;
    }

    /* --- Header / Navbar --- */
    #header {
      background: #fff;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
      padding: 15px 0;
      position: sticky;
      top: 0;
      z-index: 999;
    }

    #logo h1 {
      font-size: 28px;
      margin: 0;
      line-height: 1;
      font-weight: 700;
      letter-spacing: 1px;
    }

    #logo h1 a {
      color: #0c2e8a;
    }

    #logo h1 a span {
      color: #0d6efd;
    }

    .nav-menu a {
      color: #333;
      font-weight: 600;
      font-size: 15px;
      padding: 0 15px;
    }

    .nav-menu a:hover, .nav-menu .active {
      color: #0d6efd;
    }

    /* --- Hero Section (Carousel) --- */
    #intro {
      width: 100%;
      height: 90vh;
      position: relative;
      background: #000;
    }

    .carousel-item {
      height: 90vh;
      background-size: cover;
      background-position: center;
      position: relative;
    }

    /* Dark overlay for text readability */
    .carousel-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
    }

    .intro-content h2 {
      color: #fff;
      margin-bottom: 30px;
      font-size: 48px;
      font-weight: 700;
    }

    .intro-content span {
      color: #0d6efd;
      text-decoration: underline;
    }

    .btn-get-started {
      font-family: 'Montserrat', sans-serif;
      font-weight: 500;
      font-size: 16px;
      letter-spacing: 1px;
      display: inline-block;
      padding: 10px 30px;
      border-radius: 50px;
      transition: 0.5s;
      margin: 10px;
      color: #fff;
      background: #0d6efd;
      border: 2px solid #0d6efd;
    }

    .btn-get-started:hover {
      background: transparent;
      color: #fff;
    }

    /* --- Portfolio / Gallery --- */
    #portfolio {
      padding: 60px 0;
      background: #f9f9f9;
    }

    .section-header h2 {
      font-size: 32px;
      color: #111;
      text-transform: uppercase;
      text-align: center;
      font-weight: 700;
      position: relative;
      padding-bottom: 15px;
    }

    .section-header h2::before {
      content: '';
      position: absolute;
      display: block;
      width: 120px;
      height: 1px;
      background: #ddd;
      bottom: 1px;
      left: calc(50% - 60px);
    }
    .section-header h2::after {
      content: '';
      position: absolute;
      display: block;
      width: 40px;
      height: 3px;
      background: #0d6efd;
      bottom: 0;
      left: calc(50% - 20px);
    }

    .portfolio-item {
      position: relative;
      overflow: hidden;
      border-radius: 8px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }

    .portfolio-item img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      transition: transform 0.5s ease;
    }

    .portfolio-item:hover img {
      transform: scale(1.1);
    }

    /* --- About Section --- */
    #about {
      padding: 60px 0;
    }

    /* --- Partners --- */
    #partners img {
      max-width: 100%;
      height: auto;
      filter: grayscale(100%);
      transition: 0.3s;
      opacity: 0.7;
    }

    #partners img:hover {
      filter: grayscale(0%);
      opacity: 1;
    }

    /* --- Footer --- */
    #footer {
      background: #0c0c0c;
      padding: 30px 0;
      color: #fff;
      font-size: 14px;
      text-align: center;
    }

    #footer a {
        color: #0d6efd;
    }

    /* Mobile Responsive Tweaks */
    @media (max-width: 768px) {
      .intro-content h2 { font-size: 32px; }
      #topbar { display: none; }
    }
  </style>
</head>

<body id="body">

  <section id="topbar" class="d-none d-lg-block">
    <div class="container d-flex justify-content-between">
      <div class="contact-info">
        <i class="fa fa-envelope-o"></i> <a href="mailto:contact@tripsync.example">contact@tripsync.example</a>
        <i class="fa fa-phone"></i> +880 1122334455
      </div>
      <div class="social-links">
        <a href="#" class="mx-2"><i class="fa-brands fa-twitter"></i></a>
        <a href="#" class="mx-2"><i class="fa-brands fa-facebook"></i></a>
        <a href="#" class="mx-2"><i class="fa-brands fa-instagram"></i></a>
      </div>
    </div>
  </section>

  <header id="header">
    <nav class="navbar navbar-expand-lg navbar-light container">
      <div class="container-fluid px-0">
        <div id="logo">
          <h1><a href="index.php">Trip<span>Sync</span></a></h1>
        </div>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <ul class="navbar-nav nav-menu align-items-center">
            <li class="nav-item"><a class="nav-link active" href="#body">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
            <li class="nav-item"><a class="nav-link" href="#portfolio">Gallery</a></li>
            <li class="nav-item"><a class="nav-link" href="registration/admin.php">Admin</a></li>
            <li class="nav-item ms-lg-3">
                <a href="registration/login.php" class="btn btn-primary text-white px-4 rounded-pill">Login</a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <section id="intro">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3000">

      <div class="carousel-overlay" style="z-index: 2;">
         <div class="intro-content">
            <h2 class="animate__animated animate__fadeInDown">Making <span>your Ride</span><br>happen!</h2>
            <div class="animate__animated animate__fadeInUp">
              <a href="profile.php" class="btn-get-started scrollto">Book Ticket Now</a>
            </div>
         </div>
      </div>

      <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('img/bus1.jpg');"></div>
        <div class="carousel-item" style="background-image: url('img/bus2.jpg');"></div>
        <div class="carousel-item" style="background-image: url('img/bus3.jpg');"></div>
        <div class="carousel-item" style="background-image: url('img/bus4.jpg');"></div>
      </div>

    </div>
  </section>

  <main id="main">

    <section id="portfolio">
      <div class="container">
        <div class="section-header text-center mb-5">
          <h2>Our Fleet Gallery</h2>
          <p class="text-muted">“The Impulse to Travel is one of the hopeful symptoms of life”</p>
        </div>

        <div class="row g-4">
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port1.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?bus'">
            </div>
          </div>

          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port2.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?travel'">
            </div>
          </div>

          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port3.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?road'">
            </div>
          </div>

          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port4.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?transport'">
            </div>
          </div>

           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port5.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?highway'">
            </div>
          </div>

           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port6.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?coach'">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port7.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?trip'">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
                <img src="img/port8.jpg" alt="Bus Image" class="img-fluid" onerror="this.src='https://source.unsplash.com/400x300/?drive'">
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="partners" class="py-5 bg-white">
      <div class="container">
        <div class="section-header text-center mb-5">
          <h2>Our Partners</h2>
          <p class="text-muted">With their help and cooperation we make your travel full of joy.</p>
        </div>

        <div class="row align-items-center justify-content-center text-center">
          <div class="col-6 col-md-2 mb-4">
            <img src="img/partner1.png" class="img-fluid" alt="Partner 1">
          </div>
          <div class="col-6 col-md-2 mb-4">
            <img src="img/partners2.jpeg" class="img-fluid" alt="Partner 2">
          </div>
          <div class="col-6 col-md-2 mb-4">
            <img src="img/partner3.jpg" class="img-fluid" alt="Partner 3">
          </div>
          <div class="col-6 col-md-2 mb-4">
            <img src="img/partner4.jpg" class="img-fluid" alt="Partner 4">
          </div>
          <div class="col-6 col-md-2 mb-4">
            <img src="img/partner5.png" class="img-fluid" alt="Partner 5">
          </div>
        </div>
      </div>
    </section>

    <section id="about" class="bg-light">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
             <img src="img/port3.jpg" class="img-fluid rounded shadow" alt="About Us" onerror="this.src='https://source.unsplash.com/600x400/?office'">
          </div>
          <div class="col-lg-6 ps-lg-5">
              <h2 class="mb-3 text-primary">About TripSync</h2>
              <p class="lead">Reliable, comfortable, and affordable bus transport solutions for everyone.</p>
              <p>
                  We are dedicated to providing the best travel experience. Our modern fleet ensures
                  you reach your destination safely and on time. Whether you are traveling for work or leisure,
                  TripSync is your trusted partner on the road.
              </p>
              <ul class="list-unstyled mt-3">
                  <li><i class="fa fa-check text-success me-2"></i> Real-time Booking</li>
                  <li><i class="fa fa-check text-success me-2"></i> Verified Drivers</li>
                  <li><i class="fa fa-check text-success me-2"></i> 24/7 Customer Support</li>
              </ul>
              <a href="#portfolio" class="btn btn-outline-primary mt-3">View Our Fleet</a>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong>TripSync</strong>. All Rights Reserved
      </div>
      <div class="credits mt-2">
        Designed by <a href="#">TripSync Dev Team</a>
      </div>
    </div>
  </footer>

  <a href="#" class="btn btn-primary rounded-circle back-to-top" style="position: fixed; bottom: 20px; right: 20px; display: none;">
    <i class="fa fa-chevron-up"></i>
  </a>

  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
      // Back to top button logic
      $(window).scroll(function() {
          if ($(this).scrollTop() > 100) {
              $('.back-to-top').fadeIn();
          } else {
              $('.back-to-top').fadeOut();
          }
      });
      $('.back-to-top').click(function() {
          $('html, body').animate({scrollTop : 0}, 800);
          return false;
      });
  </script>

</body>
</html>