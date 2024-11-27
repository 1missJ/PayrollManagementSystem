<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php'; // Database connection

// Handle saving employees to the branch
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_branch'])) {
    // Make sure branch_id is set before proceeding
    if (isset($_POST['branch_id'])) {
        $branch_id = $_POST['branch_id'];
    } else {
        echo "<script>alert('Branch ID is missing!'); window.location.href='branch.php';</script>";
        exit;
    }

    // Insert selected employees to the branch
    if (isset($_POST['employees']) && !empty($_POST['employees'])) {
        $employees = $_POST['employees'];
        foreach ($employees as $employee_id) {
            $query = "INSERT INTO branch_employee (branch_id, employee_id) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $branch_id, $employee_id);
            $stmt->execute();
        }

        echo "<script>alert('Employees added to the branch successfully!'); window.location.href='branch.php';</script>";
    } else {
        echo "<script>alert('No employees selected!');</script>";
    }
}

$branch_id = isset($_GET['branch_id']) ? $_GET['branch_id'] : null;

// Get the branch details
$query = "SELECT id, department_address FROM branch WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $branch_id);
$stmt->execute();
$result = $stmt->get_result();
$branch = $result->fetch_assoc();

// Ensure that branch data is available
if (!$branch) {
    echo "<script>alert('Branch not found!'); window.location.href='branch.php';</script>";
    exit;
}

// Get the list of employees from the database
$employeeQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM employees";
$employeeResult = $conn->query($employeeQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Employees to Branch</title>
    <link rel="stylesheet" href="branch.css">
    <style>
        .employee-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .employee-item input[type="checkbox"] {
            margin-right: 10px;
        }
    </style>
    <script>
        function searchEmployee() {
            let input = document.getElementById("employeeSearch");
            let filter = input.value.toUpperCase();
            let container = document.getElementById("employeeList");
            let items = container.getElementsByClassName("employee-item");

            for (let i = 0; i < items.length; i++) {
                let item = items[i];
                let text = item.textContent || item.innerText;
                if (text.toUpperCase().indexOf(filter) > -1) {
                    item.style.display = "block";
                } else {
                    item.style.display = "none";
                }
            }
        }
    </script>
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
            Add Employees to Branch
        </div>

        <div class="Branch-form">
            <h3 style="text-align: center; font-weight: bold;">Select Employees to Add to Branch</h3>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>

            <form method="POST" action="add_employees_to_branch.php">
                <label for="branch">Branch</label>
                <input type="text" id="branch" name="branch" value="<?php echo $branch['department_address']; ?>" readonly>

                <!-- Add a hidden field for branch_id -->
                <input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">

                <label for="employeeSearch">Search Employee by ID or Name</label>
                <input type="text" id="employeeSearch" onkeyup="searchEmployee()" placeholder="Enter Employee Name">

                <label for="employees">Select Employees</label>
                <div class="multi-select-container">
                    <div class="employee-list" id="employeeList">
                        
                        <?php
while ($employee = $employeeResult->fetch_assoc()) {
    echo "<div class='employee-item'>
        <input type='checkbox' name='employees[]' value='{$employee['id']}'> 
        <label>{$employee['name']}</label>
    </div>";
}
?>

                    </div>
                </div>

                <div style="border-top: 2px solid black; margin: 20px 0;"></div>
                <div class="form-buttons">
                    <button type="reset" class="cancel-btn">CANCEL</button>
                    <button type="submit" name="select_branch" class="save-btn">SAVE</button>
                </div>
            </form>
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
    </script>
</body>
</html>
