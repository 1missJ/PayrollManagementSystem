<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #ffe4c4;
        }

        .header {
            width: 100%;
            height: 90px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff3e0;
            border-top: 1px solid #ccc;
            padding: 0 20px;
            box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
            position: fixed;
            top: 0;
            z-index: 1;
        }

        .header-left {
            display: flex;
            align-items: center;
        }

        .logo-img {
            height: 50px;
        }

        .header-right {
            display: flex;
            align-items: center;
            font-weight: bold;
        }

        .admin-text {
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .logout-icon {
            font-size: 1.5rem;
            color: black;
            text-decoration: none;
        }

        .sidebar {
            width: 250px;
            background-color: #ffa07a;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            padding-top: 110px;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar ul li {
            width: 100%;
        }

        .sidebar ul li a {
            display: flex;
            align-items: center;
            padding: 15px;
            color: black;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .sidebar ul li a:hover {
            background-color: #ff8c66;
        }

        .icon {
            margin-right: 10px;
        }

        .content {
            margin-left: 250px;
            padding-top: 90px;
            padding: 20px;
            width: calc(100% - 250px);
            height: calc(100vh - 90px);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .welcome-box {
            background-color: #ffffff;
            padding: 20px 40px;
            border: 2px solid #f4a460;
            margin-top: -400px;
            margin-left: 100px;
            padding-left: 300px;
            padding-right: 300px;
        }

        .content h1 {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
        }

        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            padding: 10px;
            background-color: #fff3e0;
            font-size: 0.9rem;
            color: #333;
            border-top: 1px solid #ccc;
        }
    </style>
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

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById("logoutOverlay").style.display = "flex";
        }

        function closeLogoutConfirmation() {
            document.getElementById("logoutOverlay").style.display = "none";
        }

        function confirmLogout() {
            window.location.href = "login.php";
        }
    </script>
</body>
</html>
