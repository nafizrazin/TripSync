<?php
session_start();
// Check login status
if(!isset($_SESSION['uid'])){
  // Since login.php is likely in the same folder (registration), we just use the filename
  header('location:login.php');
  exit();
}
$username = isset($_SESSION['uname']) ? $_SESSION['uname'] : 'Traveler';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Search - TripSync</title>

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Montserrat:wght@500;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            font-family: 'Open Sans', sans-serif;
            background-color: #f0f2f5;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        h1, h2, h3, h4 { font-family: 'Montserrat', sans-serif; }

        .navbar { background-color: #0c2e8a; }
        .navbar-brand { font-weight: bold; color: white !important; }
        .nav-link { color: rgba(255,255,255,0.9) !important; transition: 0.3s; }
        .nav-link:hover { color: #ffc107 !important; }

        .search-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
            background: white;
        }

        .main-content { margin-top: 40px; margin-bottom: 40px; flex: 1; }

        footer {
            background-color: #222;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            margin-top: auto;
        }

        /* Loading Spinner */
        #loader {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(255,255,255,0.9);
            z-index: 9999;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        /* Seat Grid Styling (Optional for results) */
        .seat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="../index.php"><i class="fa fa-bus me-2"></i>TripSync</a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item">
                        <span class="nav-link">Welcome, <?php echo htmlspecialchars($username); ?></span>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <button class="btn btn-warning btn-sm rounded-pill px-3 my_tickets">
                            <i class="fa fa-ticket me-1"></i> My Tickets
                        </button>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="nav-link text-danger" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content container">

        <div class="row justify-content-center" id="search-section">
            <div class="col-md-8 col-lg-6">
                <div class="card search-card p-4">
                    <h3 class="text-center mb-4 text-primary">Find Your Bus</h3>
                    <form id="search-form">
                        <div class="mb-3">
                            <label class="form-label text-muted">Departure</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-map-marker-alt"></i></span>
                                <input type="text" class="form-control" id="from" placeholder="From (e.g. Dhaka)" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Destination</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-location-arrow"></i></span>
                                <input type="text" class="form-control" id="to" placeholder="To (e.g. Chittagong)" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted">Date</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <input type="date" class="form-control" id="date" required>
                            </div>
                        </div>
                        <div class="d-grid mt-4">
                            <button class="btn btn-primary btn-lg bussearch" type="button">
                                <i class="fa fa-search me-2"></i> Search Buses
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="results-container" class="mt-4" style="display:none;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="text-secondary">Available Buses</h4>
                <button class="btn btn-sm btn-secondary" onclick="location.reload()">
                    <i class="fa fa-arrow-left"></i> New Search
                </button>
            </div>
            <div id="results"></div>
        </div>

        <div id="booking-container" class="row justify-content-center mt-4" style="display:none;">
            <div class="col-md-6" id="bookingres"></div>
        </div>

    </div>

    <div id="loader">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <div class="mt-2 fw-bold text-dark">Processing...</div>
    </div>

    <footer>
        <div class="container">
            <small>&copy; <?php echo date('Y'); ?> TripSync. All Rights Reserved.</small>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function(){

            // ==========================================
            // !!!  FIXED PATH: Going UP one level    !!!
            // ==========================================
            var API_PATH = '../action.php';
            // ==========================================

            // 1. Search Logic
            $('.bussearch').click(function(e){
                e.preventDefault();
                var from = $('#from').val().trim();
                var to = $('#to').val().trim();
                var date = $('#date').val();

                if(!from || !to || !date) { alert('Please fill in all fields'); return; }

                $('#loader').css('display', 'flex');

                $.ajax({
                    url: API_PATH,
                    method: 'POST',
                    data: { search: 1, from: from, to: to, date: date },
                    success: function(data){
                        $('#loader').hide();
                        $('#search-section').slideUp();
                        $('#results-container').fadeIn();
                        $('#results').html(data);
                    },
                    error: function(xhr, status, error) {
                        $('#loader').hide();
                        alert('Error: Could not connect to ' + API_PATH + '\n\nPlease check if action.php is in the "transport" folder.');
                    }
                });
            });

            // 2. Click Book Button
            $(document).on('click', '.book', function(){
                var bid = $(this).attr('bid');
                var jid = $(this).attr('jid');
                var seat = $(this).attr('seat');

                $('#loader').css('display', 'flex');

                $.ajax({
                    url: API_PATH,
                    method: 'POST',
                    data: { book: 1, bid: bid, jid: jid, seat: seat },
                    success: function(data){
                        $('#loader').hide();
                        $('#results-container').hide();
                        $('#booking-container').fadeIn();
                        $('#bookingres').html(data);
                    },
                    error: function() { $('#loader').hide(); alert('Connection Error'); }
                });
            });

            // 3. Confirm Booking
            $(document).on('click', '.booking', function(e){
                e.preventDefault();
                var bid = $(this).attr('bid');
                var jid = $(this).attr('jid');
                var seat = $(this).attr('seat');
                var name = $('#name').val();
                var no_p = $('#no').val();

                if(!name || !no_p) { alert('Enter passenger details'); return; }

                $('#loader').css('display', 'flex');

                $.ajax({
                    url: API_PATH,
                    method: 'POST',
                    data: { booking: 1, bid: bid, jid: jid, seat: seat, name: name, no_p: no_p },
                    success: function(data){
                        $('#loader').hide();
                        $('#booking-container').html(data);
                        // Button to reload the page to search again
                        $('#booking-container').append('<div class="text-center mt-3"><button onclick="location.reload()" class="btn btn-primary">Back to Dashboard</button></div>');
                    },
                    error: function() { $('#loader').hide(); alert('Booking Failed'); }
                });
            });

            // 4. View Tickets
            $('.my_tickets').click(function(){
                $('#loader').css('display', 'flex');
                $.ajax({
                    url: API_PATH,
                    method: 'POST',
                    data: { ticket: 1 },
                    success: function(data){
                        $('#loader').hide();
                        $('#search-section').hide();
                        $('#booking-container').hide();
                        $('#results-container').fadeIn();
                        $('#results-container h4').text("My Booked Tickets");
                        $('#results').html(data);
                    },
                    error: function() { $('#loader').hide(); alert('Connection Error'); }
                });
            });

        });
    </script>
</body>
</html>