<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="./css/home.css">
</head>
<body>
    <!-- header -->
    <?php include_once("header.php"); ?>

    <!-- sidebar -->
    <?php include_once("sidebar.php"); ?>

    <main class="content">
        <div class="welcome-box">
            <h1>WELCOME ADMINISTRATOR!</h1>
        </div>
    </main>

    <footer class="footer">
        © 2024 Payroll Management System. All rights reserved.
    </footer>

<?php include_once("./modal/logout-modal.php"); ?>

</body>
</html>
