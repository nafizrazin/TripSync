<?php
include('../action.php');

// If not logged in as admin, go to admin login page (in this same folder)
if(!isset($_SESSION['aname'])){
   header('location:admin.php');
   exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Bus Details</title>

    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="main">

        <div class="container">
            <div class="signup-content">
                <form method="POST" action="../action.php" id="signup-form" class="signup-form">
                    <h2>Bus Details</h2>

                    <div class="form-group">
                        <input type="text" class="form-input" name="bname" id="bname" placeholder="Bus Name" required/>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-input" name="bno" id="bno" placeholder="Bus No" required/>
                    </div>
                     <div class="form-group">
                        <input type="text" class="form-input" name="from" id="from" placeholder="From" required/>
                    </div>
                     <div class="form-group">
                        <input type="text" class="form-input" name="to" id="to" placeholder="To" required/>
                    </div>
                     <div class="form-group">
                        <label style="font-size: 12px; color: #999;">Departure Time</label>
                        <input type="time" class="form-input" name="time" id="time" required/>
                    </div>
                     <div class="form-group">
                        <input type="number" class="form-input" name="sno" id="sno" placeholder="Number of Seats" required/>
                    </div>
                     <div class="form-group">
                        <input type="number" class="form-input" name="fare" id="fare" placeholder="Fare" required/>
                    </div>
                    <div class="form-group" style="margin-top: 10px;">
                        <input type="radio" name="rad" value="AC" id="ac" checked/> <label for="ac">AC</label>
                        <input type="radio" name="rad" value="Non-AC" id="nonac" style="margin-left: 20px;"/> <label for="nonac">Non AC</label>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="bus" id="submit" class="form-submit submit" value="Add Bus"/>
                        <br><br>
                        <a href="../admin.php" style="text-decoration: none; color: #666;">&larr; Back to Dashboard</a>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>