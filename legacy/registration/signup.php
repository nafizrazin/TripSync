<?php
// Include the logic file from the parent folder (transport/action.php)
include('../action.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sign Up</title>

    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="main">

        <div class="container">
            <div class="signup-content">
                <form method="POST" action="" id="signup-form" class="signup-form">
                    <h2>Sign Up</h2>

                    <div class="form-group">
                        <input type="text" class="form-input" name="name" id="fullname" placeholder="Your Name" required/>
                    </div>
                     <div class="form-group">
                        <input type="text" class="form-input" name="uname" id="username" placeholder="Username" required/>
                    </div>
                    <div class="form-group">
                        <input type="number" class="form-input" name="age" id="age" placeholder="Age" required/>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-input" name="pno" id="phone" placeholder="Phone No." required/>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-input" name="aidno" id="nid" placeholder="NID No." required/>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-input" name="email" id="email" placeholder="Email ID" required/>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-input" name="psw" id="password" placeholder="Password" required/>
                        <span toggle="#password" class="zmdi zmdi-eye field-icon toggle-password"></span>
                    </div>

                    <div class="form-group">
                        <input type="submit" name="signup" id="submit" class="form-submit submit" value="Sign up"/>
                    </div>
                </form>

                <div style="text-align: center; margin-top: 10px;">
                    <p>Already have an account? <a href="login.php" class="login-link">Log In</a></p>
                </div>

            </div>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>