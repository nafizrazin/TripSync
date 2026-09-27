<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>

    <link rel="stylesheet" href="fonts/material-icon/css/material-design-iconic-font.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <div class="main">

        <div class="container">
            <div class="signup-content">
                <form method="POST" action="../action.php" id="signup-form" class="signup-form">
                    <h2>Admin Login</h2>
                     <div class="form-group">
                        <input type="text" class="form-input" name="uname" id="name" placeholder="Username" required/>
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-input" name="psw" id="password" placeholder="Password" required/>
                        <span toggle="#password" class="zmdi zmdi-eye field-icon toggle-password"></span>
                    </div>
                    <div class="form-group">
                        <input type="submit" name="alogin" id="submit" class="form-submit submit" value="Log In"/>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>