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
            padding-top: 90px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
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
            flex-direction: column;
        }


        .salary {
            background-color: #ffffff;
            padding: 20px;
            border: 2px solid #f4a460;
            margin-top: 100px;
            margin-bottom: 20px;
            width: 100%;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .salary-box {
            background-color: #ffffff;
            padding: 20px;
            border: 2px solid #f4a460;
            margin-top: 100px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px 40px;
            border: 2px solid #f4a460;
            width: 100%;
            max-width: 800px;
            text-align: center;
        }

        .form-container h2 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-container form {
            display: flex;
            flex-direction: column;
            margin-top: 20px;
            margin-left: 70px;
            margin-right: 70px;
            align-items: center;
        }

        .form-container input[type="text"], .form-container input[type="date"] {
            width: 100%;
            padding: 20px;
            margin: 10px 0;
            border: 1px solid #ccc;
            
            font-size: 1rem;
        }

        .form-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #ff8c66;
            color: black;
            font-weight: bold;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .form-container input[type="submit"]:hover {
            background-color: #ff8c66;
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

        .logout-overlay {
            display: none;
            background-color: rgba(0, 0, 0, 0.5);
            margin-top: 60px;
           margin-left: 1100px;
            position: fixed;
            top: 0;
            left: 0;
           
            justify-content: center;
            align-items: center;
            z-index: 2;
        }


        .logout-content {
            background-color: #fff;
            padding: 20px;
            width: 400px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .logout-header {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .logout-footer {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .confirm-btn, .cancel-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .confirm-btn {
            background-color: #ffa07a;
            color: white;
        }

        .cancel-btn {
            background-color: #ccc;
            color: black;
        }

        .delete-overlay {
    display: none;
    margin-top: -190px;
    margin-left: 100px;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    justify-content: center;
    align-items: center;
    z-index: 2;
}

.delete-content {
    background-color: #fff;
    padding: 10px;
    width: 1200px;
    border-radius: 5px;
    text-align: center;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
}

.delete-header {
    font-size: 1.5rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.delete-footer {
    display: flex;
    justify-content: space-around;
    margin-top: 20px;
}

.confirm-btn, .cancelll-btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1rem;
}

.confirm-btn {
    background-color: #ffa07a;
    color: white;
    
}

.cancel-btn {
    background-color: #ccc;
    color: black;
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
            <li class="active"><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li><a href="user.php"><i class="icon">👤</i> User</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="salary">
            SALARY SLIP
        </div>
        <div class="form-container">
            <form>
                <input type="text" placeholder="Employee ID:" required>
                <input type="text" placeholder="Name:" required>
                <input type="text" placeholder="Date From:" required>
                <input type="text" placeholder="Date To:" required>
                <input style="margin-left: 650px; width: 8vw; " type="submit" value="Submit">
            </form>
        </div>
    </main>

    <footer class="footer">
        <p>© 2024, ALL RIGHTS RESERVED</p>
    </footer>


    <div class="logout-overlay" id="logoutOverlay">
        <div class="logout-content">
            <div class="logout-header" style="padding: 20px;">Confirmation</div>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <p style="padding: 30px;">Are you sure you want to log out?</p>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <div class="logout-footer">
                <button class="cancel-btn" onclick="closeLogoutConfirmation()">No</button>
                <button class="confirm-btn" onclick="confirmLogout()">Yes</button>
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

     
        function confirmLogout() {
            window.location.href = "login.php"; 
        }
    </script>
</body>
</html>