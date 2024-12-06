<?php
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

// Handle form submission for adding deductions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_deduction'])) {
    $employee_id = $_POST['employee_id'];
    $deduction_names = $_POST['deduction_names']; 
    $deduction_amounts = $_POST['deduction_amounts'];
    $deduction_start_dates = $_POST['deduction_start_date'];
    $deduction_end_dates = $_POST['deduction_end_date'];

    foreach ($deduction_names as $index => $deduction_name) {
        $deduction_amount = $deduction_amounts[$index];
        $deduction_start_date = $deduction_start_dates[$index];
        $deduction_end_date = $deduction_end_dates[$index];

        // Insert deduction with dates
        $insertQuery = "INSERT INTO deduction (employee_id, deduction_name, amount, date, start_date, end_date, status) 
                        VALUES (?, ?, ?, NOW(), ?, ?, 'active')";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("isdss", $employee_id, $deduction_name, $deduction_amount, $deduction_start_date, $deduction_end_date);
        $stmt->execute();
    }

    echo "<script>alert('Deductions Applied Successfully!'); window.location.href='deductionList.php';</script>";
}

// Handle deletion of a deduction
if (isset($_GET['delete'])) {
    $deduction_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM deduction WHERE id = ?");
    $stmt->bind_param("i", $deduction_id);
    $stmt->execute();
    echo "<script>alert('Deduction has been deleted!'); window.location.href='deductionList.php';</script>";
}

// Handle enabling or disabling a deduction
if (isset($_GET['toggle_status']) && isset($_GET['deduction_id'])) {
    $deduction_id = $_GET['deduction_id'];
    $current_status = $_GET['status'];

    // Toggle the status
    $new_status = ($current_status === 'active') ? 'disabled' : 'active';

    // Update the deduction status
    $stmt = $conn->prepare("UPDATE deduction SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $deduction_id);
    $stmt->execute();
    echo "<script>alert('Deduction status updated!'); window.location.href='deductionList.php';</script>";
}

// Query to fetch data for deduction list
$employeeDeductionsQuery = "
    SELECT d.id AS deduction_id, d.employee_id, e.first_name, e.last_name, e.monthly_salary, 
           d.deduction_name, d.amount, d.start_date, d.end_date, d.date, d.status,
           SUM(d.amount) OVER (PARTITION BY d.employee_id) AS total_deduction
    FROM deduction d 
    INNER JOIN employees e ON d.employee_id = e.id
";

// Query to fetch employees who don't have deductions yet
$employeeQuery = "
    SELECT id, CONCAT(first_name, ' ', last_name) AS name
    FROM employees
    WHERE id NOT IN (SELECT DISTINCT employee_id FROM deduction)
";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deduction List</title>
    <link rel="stylesheet" href="./css/deduction.css">
    <style>
        .custom-select-box {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .deduction-checkbox {
            margin-right: 10px;
            vertical-align: middle;
        }

        .deduction-checkbox + label {
            vertical-align: middle;
        }

        .custom-select-box label {
            margin-bottom: 5px;
            white-space: nowrap;
        }

        /* Styled buttons */
        .toggle-status, .delete-btn {
            padding: 8px 16px;
            border: none;
            cursor: pointer;
            text-align: center;
            font-weight: bold;
            border-radius: 4px;
        }

        /* Enable/Disable button styles */
        .toggle-status.active {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        .toggle-status.disabled {
            background-color: #FF5722; /* Red */
            color: white;
        }

        /* Delete button style */
        .delete-btn {
            background-color: #f44336; /* Red */
            color: white;
        }
    </style>
</head>
<body>

<?php include_once("header.php"); ?>
<?php include_once("sidebar.php"); ?>

<main class="content">
    <div class="deduction-box">Deduction List</div>
    <div class="deduction-form">
        <h3 style="text-align: center;">DEDUCTION FORM</h3>
        <button onclick="window.location.href='add_deduction.php'" type="button">Add/Remove Deduction</button>
        
        <form method="POST" action="deductionList.php">
            <label for="employee-id">Employee</label>
            <select id="employee-id" name="employee_id" required>
                <option value="">Select Employee</option>
                <?php
                $employeeResult = $conn->query($employeeQuery);
                while ($employee = $employeeResult->fetch_assoc()) {
                    echo "<option value='{$employee['id']}'>{$employee['name']}</option>";
                }
                ?>
            </select>

            <label for="deduction-names">Deductions</label>
            <div class="custom-select-box">
                <div class="dropdown-content">
                    <?php
                    $customDeductionsQuery = "SELECT name FROM custom_deductions";
                    $customDeductionsResult = $conn->query($customDeductionsQuery);
                    while ($customDeduction = $customDeductionsResult->fetch_assoc()) {
                        echo "<label><input type='checkbox' class='deduction-checkbox' name='deduction_names[]' value='{$customDeduction['name']}'> {$customDeduction['name']}</label>";
                    }
                    ?>
                </div>
            </div>

            <div id="deduction-amounts"></div>

            <label for="deduction-date">Date Applied</label>
            <input type="date" id="deduction-date" name="deduction_date" required>

            <button type="submit" name="save_deduction">APPLY DEDUCTION</button>
        </form>
    </div>

    <table class="deduction-table">
    <thead>
        <tr>
            <th>Employee ID</th>
            <th>Employee Name</th>
            <th>Salary</th>
            <th>Deduction Name</th>
            <th>Amount</th>
            <th>Start Date</th>
            <th>End Date</th>
            <th>Date Applied</th>
            <th>Net Pay</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $deductionResults = $conn->query($employeeDeductionsQuery);
        $current_employee = null;
        $net_pay = 0;

        if ($deductionResults) {
            while ($row = $deductionResults->fetch_assoc()) {
                if ($current_employee !== $row['employee_id']) {
                    $net_pay = $row['monthly_salary'] - $row['total_deduction'];
                    $current_employee = $row['employee_id'];
                }
                echo "<tr>
                        <td>{$row['employee_id']}</td>
                        <td>{$row['first_name']} {$row['last_name']}</td>
                        <td>{$row['monthly_salary']}</td>
                        <td>{$row['deduction_name']}</td>
                        <td>{$row['amount']}</td>
                        <td>{$row['start_date']}</td>
                        <td>{$row['end_date']}</td>
                        <td>{$row['date']}</td>
                        <td>$net_pay</td>
                        <td>" . ucfirst($row['status']) . "</td>
                        <td>
                            <a href='deductionList.php?toggle_status=1&deduction_id={$row['deduction_id']}&status={$row['status']}' class='toggle-status " . ($row['status'] == 'active' ? 'active' : 'disabled') . "'>
                                " . ($row['status'] == 'active' ? 'Disable' : 'Enable') . "
                            </a>
                        </td>
                      </tr>";
            }
        } else {
            echo "<tr><td colspan='11'>No deductions found.</td></tr>";
        }
        ?>
    </tbody>
</table>

</main>

<?php include_once("./modal/logout-modal.php"); ?>

<script>
    document.querySelectorAll('.deduction-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            let deductionAmountFields = document.getElementById('deduction-amounts');
            if (checkbox.checked) {
                let amountInput = document.createElement('div');
                amountInput.innerHTML = `
                    <label for="amount-${checkbox.value}">Amount for ${checkbox.value}</label>
                    <input type="number" id="amount-${checkbox.value}" name="deduction_amounts[]" required>
                    <label>Start Date: <input type="date" name="deduction_start_date[]" required></label>
                    <label>End Date: <input type="date" name="deduction_end_date[]" required></label>`;
                deductionAmountFields.appendChild(amountInput);
            } else {
                let inputField = document.querySelector(`#amount-${checkbox.value}`).parentElement;
                inputField.remove();
            }
        });
    });
</script>

</body>
</html>
