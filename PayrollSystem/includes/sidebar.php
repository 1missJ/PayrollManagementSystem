<style>
     /* General Styling */
     * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
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
            padding-top: 100px;
            padding: 20px;
            width: calc(100% - 250px);
            height: calc(100vh - 60px);
            overflow-y: auto;
            background-color: #ffe4c4;
        }
</style>



<div class="sidebar">
    <ul>
        <li><a href="home.php">🏠 Home</a></li>
        <li><a href="payrollList.php">💸 Payroll</a></li>
        <li><a href="employee.php">👥 Employees</a></li>
        <li><a href="branch.php">🏢 Branch</a></li>
        <li><a href="deductionList.php">➖ Deduction</a></li>
        <li><a href="salarySlip.php">📄 Salary Slip</a></li>
        <li><a href="user.php">👤 Profile</a></li>
    </ul>
</div>
