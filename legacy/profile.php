<?php
session_start();
if(!isset($_SESSION['uid'])){
  header('location:registration/login.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>TripSync - Home</title>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* --- Modern Reset & Base --- */
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #f8f9fa;
      color: #444;
      overflow-x: hidden;
    }
    a { text-decoration: none; }
    h1, h2, h3, h4, h5, h6 { font-family: 'Montserrat', sans-serif; }

    /* --- Topbar --- */
    #topbar {
      background: #111;
      color: #fff;
      padding: 10px 0;
      font-size: 14px;
    }
    #topbar a { color: #fff; margin-left: 10px; transition: 0.3s; }
    #topbar a:hover { color: #0d6efd; }

    /* --- Navbar --- */
    .navbar {
      background: #fff;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05);
      padding: 15px 0;
    }
    .navbar-brand {
      font-size: 26px;
      font-weight: 700;
      color: #111 !important;
      text-transform: uppercase;
    }
    .navbar-brand span { color: #0d6efd; }
    .nav-link {
      font-weight: 600;
      color: #333 !important;
      margin-left: 15px;
      transition: 0.3s;
    }
    .nav-link:hover, .nav-link.active { color: #0d6efd !important; }

    /* --- Hero/Intro Section --- */
    #intro {
      position: relative;
      height: 90vh;
      background: #000;
      overflow: hidden;
    }
    .carousel-item {
      height: 90vh;
      background-size: cover;
      background-position: center;
    }
    /* Dark Overlay */
    .overlay {
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      color: #fff;
    }
    #intro h2 {
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
    }
    #intro h2 span { color: #0d6efd; }
    .btn-get-started {
      padding: 12px 35px;
      border-radius: 50px;
      background: #0d6efd;
      color: #fff;
      font-weight: 600;
      font-size: 1rem;
      transition: 0.3s;
      border: 2px solid #0d6efd;
    }
    .btn-get-started:hover {
      background: transparent;
      color: #fff;
    }

    /* --- Sections --- */
    section { padding: 80px 0; }
    .section-header { text-align: center; margin-bottom: 50px; }
    .section-header h2 {
      font-size: 32px;
      font-weight: 700;
      margin-bottom: 15px;
      position: relative;
      display: inline-block;
    }
    .section-header h2::after {
      content: "";
      display: block;
      width: 60px;
      height: 3px;
      background: #0d6efd;
      margin: 10px auto 0;
    }
    .section-header p { color: #777; }

    /* --- Portfolio Cards --- */
    .portfolio-item {
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 5px 15px rgba(0,0,0,0.08);
      background: #fff;
      transition: transform 0.3s ease;
      height: 100%;
    }
    .portfolio-item:hover { transform: translateY(-5px); }
    .portfolio-item img {
      width: 100%;
      height: 250px; /* Fixed height for consistency */
      object-fit: cover;
    }

    /* --- Footer --- */
    #footer {
      background: #111;
      color: #fff;
      padding: 40px 0;
      text-align: center;
    }
    #footer p { margin: 0; font-size: 14px; }
    #footer a { color: #0d6efd; }

    /* --- Responsive Tweaks --- */
    @media (max-width: 768px) {
      #intro h2 { font-size: 2.5rem; }
      .carousel-item, #intro { height: 70vh; }
    }
  </style>
</head>

<body id="body">

  <div id="topbar" class="d-none d-lg-block">
    <div class="container d-flex justify-content-between">
      <div class="contact-info">
        <i class="fa fa-envelope"></i> <a href="mailto:contact@tripsync.example">contact@tripsync.example</a>
        <i class="fa fa-phone ms-3"></i> +880 1122334455
      </div>
      <div>Welcome, <?php echo htmlspecialchars($_SESSION['uname']); ?></div>
    </div>
  </div>

  <header id="header" class="sticky-top">
    <nav class="navbar navbar-expand-lg">
      <div class="container">
        <a class="navbar-brand" href="#body">Trip<span>Sync</span></a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarMenu">
          <ul class="navbar-nav align-items-center">
            <li class="nav-item"><a class="nav-link active" href="#body">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
            <li class="nav-item"><a class="nav-link" href="ticket.php">Your Tickets</a></li>
            <li class="nav-item"><span class="nav-link fw-bold text-dark d-lg-none">User: <?php echo $_SESSION['uname'];?></span></li>
            <li class="nav-item ms-lg-3"><a class="btn btn-outline-danger btn-sm rounded-pill px-4" href="registration/logout.php">Logout</a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <section id="intro" class="p-0">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
      <div class="carousel-inner">
        <div class="carousel-item active" style="background-image: url('img/bus1.jpg'); background-color: #333;">
          <div class="overlay">
            <div class="container">
              <h2>Making <span>your Ride</span><br>happen!</h2>
              <a href="registration/bussearch.php" class="btn-get-started">Book Ticket Now</a>
            </div>
          </div>
        </div>
        <div class="carousel-item" style="background-image: url('img/bus2.jpg'); background-color: #333;">
          <div class="overlay">
            <div class="container">
              <h2>Safe & <span>Comfortable</span><br>Journeys</h2>
              <a href="registration/bussearch.php" class="btn-get-started">Book Ticket Now</a>
            </div>
          </div>
        </div>
        <div class="carousel-item" style="background-image: url('img/bus3.jpg'); background-color: #333;">
           <div class="overlay">
            <div class="container">
              <h2>Explore <span>The World</span><br>With Us</h2>
              <a href="registration/bussearch.php" class="btn-get-started">Book Ticket Now</a>
            </div>
          </div>
        </div>
      </div>

      <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
      </button>
    </div>
  </section>

  <main id="main">

    <section id="portfolio">
      <div class="container">
        <div class="section-header">
          <h2>Our Portfolio</h2>
          <p>“The Impulse to Travel is one of the hopeful symptoms of life”</p>
        </div>

        <div class="row g-4">
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port1.jpg" alt="Portfolio 1">
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port2.jpg" alt="Portfolio 2">
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port3.jpg" alt="Portfolio 3">
            </div>
          </div>
          <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port4.jpg" alt="Portfolio 4">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port5.jpg" alt="Portfolio 5">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port6.jpg" alt="Portfolio 6">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port7.jpg" alt="Portfolio 7">
            </div>
          </div>
           <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="portfolio-item">
              <img src="img/port8.jpg" alt="Portfolio 8">
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="Partners" style="background: #fff;">
      <div class="container">
        <div class="section-header">
          <h2>Our Partners</h2>
          <p>With their Help and Cooperation we make your travel full of Joy and Memorable</p>
        </div>

        <div class="row justify-content-center align-items-center text-center g-4">
          <div class="col-6 col-md-2"><img src="img/partner1.png" class="img-fluid" style="opacity: 0.6; filter: grayscale(100%);"></div>
          <div class="col-6 col-md-2"><img src="img/partners2.jpeg" class="img-fluid" style="opacity: 0.6; filter: grayscale(100%);"></div>
          <div class="col-6 col-md-2"><img src="img/partner3.jpg" class="img-fluid" style="opacity: 0.6; filter: grayscale(100%);"></div>
          <div class="col-6 col-md-2"><img src="img/partner4.jpg" class="img-fluid" style="opacity: 0.6; filter: grayscale(100%);"></div>
          <div class="col-6 col-md-2"><img src="img/partner5.png" class="img-fluid" style="opacity: 0.6; filter: grayscale(100%);"></div>
        </div>
      </div>
    </section>

    <section id="about">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6 mb-4 mb-lg-0">
            <img src="img/port3.jpg" alt="About" class="img-fluid rounded shadow w-100">
          </div>
          <div class="col-lg-6 ps-lg-5">
            <h3>Who We Are</h3>
            <p class="lead">We provide the safest and most comfortable transport services across the country.</p>
            <p>Our fleet of modern buses ensures that you reach your destination on time, every time. Book your tickets easily online and track your journey.</p>
          </div>
        </div>
      </div>
    </section>

  </main>

  <footer id="footer">
    <div class="container">
      <div class="copyright">
        &copy; Copyright <strong>Traveler</strong>. All Rights Reserved
      </div>
      <div class="credits mt-2">
        Designed by <a href="#">TripSync</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>