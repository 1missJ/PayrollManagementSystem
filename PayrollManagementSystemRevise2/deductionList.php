<?php
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php'; // Include database connection

// Function to compute deductions
function computeDeduction($salary, $deduction_name) {
    switch ($deduction_name) {
        case 'tax':
            // Compute tax based on the progressive tax rates
            if ($salary <= 10000) {
                return $salary * 0.05; // 5% tax for salary <= 10,000
            } elseif ($salary <= 30000) {
                return 10000 * 0.05 + ($salary - 10000) * 0.10; // 5% for the first 10,000, then 10% for the next
            } elseif ($salary <= 70000) {
                return 10000 * 0.05 + 20000 * 0.10 + ($salary - 30000) * 0.20; // 5% for the first 10,000, 10% for the next 20,000, then 20%
            } elseif ($salary <= 140000) {
                return 10000 * 0.05 + 20000 * 0.10 + 40000 * 0.20 + ($salary - 70000) * 0.25; // Progressive tax rates
            } elseif ($salary <= 250000) {
                return 10000 * 0.05 + 20000 * 0.10 + 40000 * 0.20 + 70000 * 0.25 + ($salary - 140000) * 0.30;
            } else {
                return 10000 * 0.05 + 20000 * 0.10 + 40000 * 0.20 + 70000 * 0.25 + 110000 * 0.30 + ($salary - 250000) * 0.35;
            }

        case 'pagibig':
            // Pag-IBIG deduction is 1% of salary (or ₱100 if salary > ₱5,000)
            return $salary > 5000 ? 100 : $salary * 0.01;

        case 'insurance':
            // Insurance deduction is 2.25% of salary
            return $salary * 0.0225;

        default:
            return 0;
    }
}



// Handle form submission for adding a deduction
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_deduction'])) {
    $employee_id = $_POST['employee_id'];
    $deduction_name = $_POST['deduction_name'];

    // Fetch the employee's monthly salary
    $salaryQuery = "SELECT monthly_salary FROM employees WHERE id = ?";
    $stmt = $conn->prepare($salaryQuery);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($employee_salary);
    $stmt->fetch();

    // Close the previous statement
    $stmt->close();

    // Compute the deduction based on selection
    $amount = computeDeduction($employee_salary, $deduction_name);

    // Insert the deduction into the database
    $query = "INSERT INTO deduction (employee_id, deduction_name, amount) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isi", $employee_id, $deduction_name, $amount);

    if ($stmt->execute()) {
        // After insertion, calculate and update net pay
        $updateQuery = "UPDATE employees SET net_salary = monthly_salary - (SELECT SUM(amount) FROM deduction WHERE employee_id = ?) WHERE id = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("ii", $employee_id, $employee_id);
        $updateStmt->execute();
        echo "<script>alert('Deduction added successfully!'); window.location.href='deductionList.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    // Close the insert statement
    $stmt->close();
}

// Handle deletion of a deduction
if (isset($_GET['delete'])) {
    $deduction_id = (int)$_GET['delete'];
    $delete_query = "DELETE FROM deduction WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $deduction_id);

    if ($stmt->execute()) {
        echo "<script>alert('Deduction deleted successfully!'); window.location.href='deductionList.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }

    // Close the delete statement
    $stmt->close();
}

// Fetch data for the deduction list and net pay computation
$query = "SELECT 
            d.id, 
            d.employee_id, 
            d.deduction_name, 
            d.amount, 
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
            e.monthly_salary AS employee_salary
          FROM deduction d 
          INNER JOIN employees e ON d.employee_id = e.id";
$result = $conn->query($query);

// Initialize an array to track total deductions for each employee
$deduction_totals = [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deduction List</title>
    <link rel="stylesheet" href="./css/deduction.css">
</head>
<body>

<!-- Header -->
 <?php include_once("header.php"); ?>

<!-- Sidebar -->
<?php include_once("sidebar.php"); ?>

<!-- Main Content -->
<main class="content">
    <div class="deduction-box">Deduction List</div>

    <!-- Deduction Form -->
    <div class="deduction-form">
        <h3 style="text-align: center;">DEDUCTION FORM</h3>
        <form method="POST" action="deductionList.php">
            <label for="employee-id">Employee ID</label>
            <select id="employee-id" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php
                $employeeQuery = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM employees";
                $employeeResult = $conn->query($employeeQuery);
                while ($employee = $employeeResult->fetch_assoc()) {
                    echo "<option value='{$employee['id']}'>{$employee['name']}</option>";
                }
                ?>
            </select>

            <label for="deduction-name">Deduction Name</label>
            <select id="deduction-name" name="deduction_name" required>
                <option value="">Select Deduction</option>
                <option value="tax">Tax</option>
                <option value="insurance">Insurance</option>
                <option value="pagibig">Pag-IBIG</option>
            </select>

            <button type="submit" name="save_deduction">SAVE</button>
        </form>
    </div>
    <div class="controls">
        <div class="search-container">
            <input type="text" placeholder="Search" class="search-bar">
            <button class="search-btn">🔍</button>
        </div>
    </div>
    <!-- Deduction List Table -->
    <table class="deduction-table">
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Employee Name</th>
                <th>Salary</th>
                <th>Deduction Information</th>
                <th>Net Pay</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = $result->fetch_assoc()) {
                // Accumulate total deductions for each employee
                $deduction_totals[$row['employee_id']] = ($deduction_totals[$row['employee_id']] ?? 0) + $row['amount'];
                $net_pay = $row['employee_salary'] - $deduction_totals[$row['employee_id']];
                echo "<tr>
                        <td>{$row['employee_id']}</td>
                        <td>{$row['employee_name']}</td>
                        <td>{$row['employee_salary']}</td>
                        <td>
                            Deduction: {$row['deduction_name']}<br>
                            Amount: {$row['amount']}
                        </td>
                        <td>{$net_pay}</td>
                        <td>

                    <a href='deductionList.php?delete={$row['id']}' class='delete' onclick='return confirm(\"Are you sure you want to delete?\");'>
                        <img src='delete.png' alt='delete' style='height: 20px; width: 25px; color: red'>
                    </a>
                        </td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</main>
<?php include_once("./modal/logout-modal.php"); ?>
</body>
</html>
