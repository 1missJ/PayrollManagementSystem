<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch List</title>
    <style>
        /* General Styling */
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
            padding-top: 110px;
            background-color: #ffa07a;
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

        .sidebar ul li.active a {
            background-color: #ff8c66;
        }

        .icon {
            margin-right: 10px;
        }

        .content {
            margin-left: 250px;
            padding-top: 100px;
            padding: 20px;
            width: calc(100% - 250px);
            height: calc(100vh - 60px);
            overflow-y: auto;
            background-color: #ffe4c4;
        }

        .Branch-box {
            background-color: #ffffff;
            padding: 20px;
            border: 2px solid #f4a460;
            margin-top: 100px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .Branch-form {
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #f4a460;
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            margin-top: 20px;
        }

        .Branch-form input, .Branch-form select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 1rem;
        }

        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .save-btn {
            background-color: #ffa07a;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-btn {
            background-color: #ccc;
            color: black;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .Branch-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .Branch-table th, .Branch-table td {
            border: 1px solid #ccc;
            background-color: white;
            padding: 10px;
            text-align: center;
        }

        .Branch-table th {
            background-color: white;
            color: black;
            font-weight: bold;
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
            z-index: 1;
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
            <li class="active"><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deductionList.php"><i class="icon">➖</i> Deduction</a></li>
            <li><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li><a href="user.php"><i class="icon">👤</i> User</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="Branch-box">
            Branch
        </div>
        <div class="Branch-form">
            <h3 style="text-align: center; font-weight: bold;">Add Branch</h3>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <label for="department-manager">Department Manager</label>
            <input type="text" id="department-manager" required>
            <label for="department-address">Department Address</label>
            <input type="text" id="department-address" required>
            <div style="border-top: 2px solid black; margin: 20px 0;"></div>
            <div class="form-buttons">
                <button class="cancel-btn" onclick="alert('Canceled')">CANCEL</button>
                <button class="save-btn" onclick="alert('Saved')">SAVE</button>
            </div>
        </div>
        <table class="Branch-table">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Branch Information</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <button class="view"><img src="eye.png" alt="eye"></button>
                        <a href="#" class="delete"><img src="delete.png" alt="delete" style="height: 20px; width: 25px;"></a>
                    </td>
                </tr>
            </tbody>
        </table>
    </main>

    <footer class="footer">
        © 2024, ALL RIGHTS RESERVED
    </footer>

    <script>
        function showLogoutConfirmation(event) {
            event.preventDefault();
            alert("Logout confirmed.");
        }
    </script>
</body>
</html>
