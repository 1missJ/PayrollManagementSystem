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
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header class="header">
        <div class="header-left">
            <img src="logo.png" alt="Logo" class="logo-img" style="height: 10vh; margin-right: -505px;">
        </div>
        <div class="header-right">
            <span class="admin-text">ADMINISTRATOR</span>
            <a href="#" class="logout-icon" onclick="showLogoutConfirmation(event)">
                <img src="logout.png" alt="logout" style="height: 25px;">
            </a>
        </div>
    </header>

    <div class="sidebar">
        <ul>
            <li><a href="home.php"><i class="icon">🏠</i> Home</a></li>
            <li><a href="payrollList.php"><i class="icon">💸</i> Payroll</a></li>
            <li><a href="employee.php"><i class="icon">👥</i> Employees</a></li>
            <li><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deductionList.php"><i class="icon">➖</i> Deduction</a></li>
            <li><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li><a href="user.php"><i class="icon">👤</i> User</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="welcome-box">
            <h1>WELCOME ADMINISTRATOR!</h1>
        </div>
    </main>

    <footer class="footer">
        © 2024 Payroll Management System. All rights reserved.
    </footer>

    <div class="logout-overlay" id="logoutOverlay">
        <div class="logout-content">
            <div class="logout-header" style="padding: 20px;">Confirmation</div>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <p style="padding: 30px;">Are you sure you want to log out?</p>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <div class="logout-footer">
                <button class="cancel-btn" onclick="closeLogoutConfirmation()">No</button>
                <a href="logout.php?logout=confirm"><button class="confirm-btn">Yes</button></a>
            </div>
        </div>
    </div>

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById("logoutOverlay").style.display = "flex";
        }

        function closeLogoutConfirmation() {
            document.getElementById("logoutOverlay").style.display = "none";
        }
        function showLogoutModal() {
            document.getElementById('logoutOverlay').style.display = 'flex';
        }
        function closeLogoutConfirmation() {
            document.getElementById('logoutOverlay').style.display = 'none';
        }
    </script>
</body>
</html>
