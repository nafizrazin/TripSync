<?php
session_start();
session_destroy();
header('location:registration/admin.php'); // Go back to login
?>