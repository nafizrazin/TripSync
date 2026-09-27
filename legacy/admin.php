<?php
session_start();
// Check if admin is logged in
if(!isset($_SESSION['aname'])){
  header('location:registration/admin.php');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard - TripSync</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" rel="stylesheet">

  <style>
      body { background-color: #f4f7f6; }
      .card { margin-bottom: 20px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
      .header-title { padding: 20px 0; }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">TripSync Admin</a>
      <div class="ml-auto">
        <span class="text-white mr-3">Welcome, <?php echo $_SESSION['aname']; ?></span>
        <a href="logout.php" class="btn btn-danger btn-sm">Log Out</a>
      </div>
    </div>
  </nav>

  <div class="container mt-5">

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <h3>Management Options</h3>
                    <p>Click below to add new buses to the schedule.</p>

                    <a href="registration/busdetails.php" class="btn btn-success btn-lg">
                        <i class="fa fa-plus-circle"></i> Add New Bus Info
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
       <div class="col-md-6 offset-md-3">
        <div class="card">
          <div class="card-header bg-info text-white">
            <h4 class="mb-0">Check Passenger List</h4>
          </div>
          <div class="card-body">
            <form id="reportForm">
             <div class="form-group">
                   <label>Bus ID / Number</label>
                   <input type="text" class="form-control" id="bid" placeholder="Enter Bus ID" required/>
             </div>
             <div class="form-group">
                   <label>Journey Date</label>
                   <input type="date" class="form-control" id="pdate" required/>
             </div>
             <button type="button" class="btn btn-dark btn-block" id="getReport">Get Details</button>
            </form>
          </div>
        </div>
       </div>
    </div>

    <div class="row mt-3" id="resultSection" style="display:none;">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Passenger Details</h4>
                </div>
                <div class="card-body">
                    <div id="bdresult"></div>
                </div>
            </div>
        </div>
    </div>

  </div>

  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function(){
        // When clicking "Get Details"
        $('#getReport').click(function(){
            var bid = $('#bid').val();
            var date = $('#pdate').val();

            if(bid == '' || date == '') {
                alert("Please enter both Bus ID and Date");
                return;
            }

            // AJAX call to action.php
            $.ajax({
                url: "action.php", // This is in the same folder as admin.php
                type: "POST",
                data: {
                    tbkd: true, // This triggers the report logic in action.php
                    bid: bid,
                    date: date
                },
                success: function(data){
                    $('#resultSection').show();
                    $('#bdresult').html(data);
                }
            });
        });
    });
  </script>

</body>
</html>