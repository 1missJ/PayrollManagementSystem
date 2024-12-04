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
    $deduction_names = $_POST['deduction_names'];  // Array of selected deductions
    $deduction_date = $_POST['deduction_date'];    // Selected deduction date
    $deduction_amounts = $_POST['deduction_amounts']; // Deduction amounts entered by the user

    foreach ($deduction_names as $index => $deduction_name) {
        // Get the amount from the array using index
        $deduction_amount = $deduction_amounts[$index];

        // Insert deduction with date and amount
        $insertQuery = "INSERT INTO deduction (employee_id, deduction_name, amount, date) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("isds", $employee_id, $deduction_name, $deduction_amount, $deduction_date);
        $stmt->execute();
    }

    echo "<script>alert('Deductions Applied Successfully!'); window.location.href='deductionList.php';</script>";
}

// Handle deletion of a deduction
if (isset($_GET['delete'])) {
    $employee_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM deduction WHERE employee_id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    echo "<script>alert('All deductions for this employee have been deleted!'); window.location.href='deductionList.php';</script>";
}

// Query to fetch data for deduction list
$employeeDeductionsQuery = "
    SELECT d.id AS deduction_id, d.employee_id, e.first_name, e.last_name, e.monthly_salary, 
           GROUP_CONCAT(CONCAT(d.deduction_name, ': ', d.amount) SEPARATOR ', ') AS deductions, 
           SUM(d.amount) AS total_deduction, MAX(d.date) AS latest_date
    FROM deduction d 
    INNER JOIN employees e ON d.employee_id = e.id
    GROUP BY d.employee_id
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
        /* Styling for checkboxes to display them in a row */
        .custom-select-box {
            display: flex;
            flex-wrap: wrap; /* Allows the items to wrap into new lines */
            gap: 10px; /* Space between checkboxes */
        }

        .deduction-checkbox {
            margin-right: 10px;
            vertical-align: middle;
        }

        .deduction-checkbox + label {
            vertical-align: middle;
        }

        /* Additional styling for proper alignment and spacing in labels */
        .custom-select-box label {
            margin-bottom: 5px;
            white-space: nowrap; /* Prevents text from breaking into multiple lines */
        }

        /* Add space between words in labels */
        .custom-select-box label {
            padding: 0 5px; /* Adds space between each word in the label */
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
                        echo "<label><input type='checkbox' class='deduction-checkbox' name='deduction_names[]' value='{$customDeduction['name']}' 
                            data-deduction-name='{$customDeduction['name']}'> {$customDeduction['name']}</label>";
                    }
                    ?>
                </div>
            </div>

            <!-- Deduction Amount Fields -->
            <div id="deduction-amounts"></div>

            <label for="deduction-date">Deduction Date</label>
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
                <th>Deduction</th>
                <th>Date</th>
                <th>Net Pay</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
    <?php
    // Fetch the results for the deduction list
    $deductionResults = $conn->query($employeeDeductionsQuery);
    if ($deductionResults) {
        while ($row = $deductionResults->fetch_assoc()) {
            // Correct calculation of net pay (subtract total deduction from salary)
            $net_pay = $row['monthly_salary'] - $row['total_deduction'];
            echo "<tr>
                    <td>{$row['employee_id']}</td>
                    <td>{$row['first_name']} {$row['last_name']}</td>
                    <td>{$row['monthly_salary']}</td>
                    <td>{$row['deductions']}</td>
                    <td>{$row['latest_date']}</td>
                    <td>$net_pay</td>
                    <td>";
                    // Generate a single delete button for each row
                    echo "<a href='deductionList.php?delete={$row['employee_id']}' class='delete'>Delete</a>";
            echo "</td></tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No deductions found.</td></tr>";
    }
    ?>
</tbody>
    </table>
</main>

<?php include_once("./modal/logout-modal.php"); ?>

<script>
    // This JavaScript will dynamically add input fields for deduction amounts based on selected checkboxes
    document.querySelectorAll('.deduction-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            let deductionAmountFields = document.getElementById('deduction-amounts');
            if (checkbox.checked) {
                // Add input field for the selected deduction
                let amountInput = document.createElement('div');
                amountInput.classList.add('deduction-amount-input');
                amountInput.innerHTML = `<label for="amount-${checkbox.value}">Amount for ${checkbox.dataset.deductionName}</label>
                                         <input type="number" id="amount-${checkbox.value}" name="deduction_amounts[]" required>`;
                deductionAmountFields.appendChild(amountInput);
            } else {
                // Remove input field for the unselected deduction
                let amountInput = document.querySelector(`#amount-${checkbox.value}`).parentElement;
                if (amountInput) {
                    amountInput.remove();
                }
            }
        });
    });
</script>

</body>
</html>
