<?php
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "payrollsystem";

$conn = new mysqli($servername, $username, $password, $database);

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch employee salary slip details (if employee_id is passed via GET)
$employeeData = null;
$deductionsData = [];
if (isset($_GET['employee_id'])) {
    $employeeId = $_GET['employee_id'];
    $salaryDetailsQuery = "
        SELECT 
            e.id, 
            CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
            e.contact_num, 
            e.position, 
            e.monthly_salary, 
            (e.monthly_salary - COALESCE(SUM(d.amount), 0)) AS net_salary, 
            e.email, 
            b.branch_name, 
            b.department_manager, 
            b.department_address,
            COALESCE(SUM(d.amount), 0) AS total_deductions,
            p.id AS payroll_id,
            p.date_from,  -- Added date_from
            p.date_to     -- Added date_to
        FROM employees e
        LEFT JOIN branch_employee be ON e.id = be.employee_id
        LEFT JOIN branch b ON be.branch_id = b.id
        LEFT JOIN deduction d ON e.id = d.employee_id
        LEFT JOIN payroll p ON e.id = p.employee_id  -- Assuming employee_id is a foreign key in payroll
        WHERE e.id = ?
        GROUP BY e.id, p.id
    ";

    $stmt = $conn->prepare($salaryDetailsQuery);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $employeeData = $result->fetch_assoc();
    }

    // Fetch all deductions for the employee
    $deductionsQuery = "
        SELECT d.deduction_name, d.amount
        FROM deduction d
        WHERE d.employee_id = ?
    ";
    $stmt = $conn->prepare($deductionsQuery);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $deductionsResult = $stmt->get_result();

    while ($row = $deductionsResult->fetch_assoc()) {
        $deductionsData[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary Slip</title>
    <link rel="stylesheet" href="./assets/css/salarySlip.css">
    <style> 
    /* Modal background */
    .salary-modal {
        display: none; /* Initial state - hidden */
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
    }

    /* Modal content box */
    .salary-modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: #fff;
        padding: 30px;
        border-radius: 8px;
        width: 80%;
        max-width: 600px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        font-family: Arial, sans-serif;
    }

    /* Button styles */
    .modal-buttons button {
        padding: 12px 25px;
        margin: 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
    }

    .modal-buttons button:hover {
        background-color: #45a049;
    }

    .modal-buttons .close-btn {
        background-color: #f44336;
    }

    .modal-buttons .send-btn {
        background-color: #008CBA;
    }

    .salary-details h3 {
        font-size: 18px;
        margin-top: 20px;
        font-weight: bold;
    }

    .salary-details p {
        font-size: 16px;
        line-height: 1.5;
    }
    </style>
</head>
<body>
    <!-- header -->
    <?php include_once("./includes/header.php"); ?>

    <!-- sidebar -->
    <?php include_once("./includes/sidebar.php"); ?>

    <main class="content">
        <div class="salary-box">Salary Slip</div>
        <div class="controls">
            <div class="search-container">
                <input type="text" placeholder="Search" class="search-bar">
                <button class="search-btn">🔍</button>
            </div>
        </div>
        <table class="form-container">
            <thead>
                <tr>
                    <th>Employee ID</th>
                    <th>Employee Name</th>
                    <th>Monthly Salary</th>
                    <th>Total Deductions</th>
                    <th>Net Salary</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $salaryDetailsQuery = "
                        SELECT 
                            e.id, 
                            CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
                            e.monthly_salary, 
                            (e.monthly_salary - COALESCE(SUM(d.amount), 0)) AS net_salary, 
                            COALESCE(SUM(d.amount), 0) AS total_deductions
                        FROM employees e
                        LEFT JOIN deduction d ON e.id = d.employee_id
                        GROUP BY e.id
                    ";
                    $result = $conn->query($salaryDetailsQuery);

                    if ($result->num_rows > 0):
                        while ($row = $result->fetch_assoc()):
                ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['employee_name']) ?></td>
                        <td>₱<?= number_format($row['monthly_salary'], 2) ?></td>
                        <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
                        <td>₱<?= number_format($row['net_salary'], 2) ?></td>
                        <td>
                            <a href="salarySlip.php?employee_id=<?= $row['id'] ?>" class="view">View</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No salary data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </main>

    <!-- footer -->
    <?php include_once("./includes/footer.php"); ?>

    <!-- Salary Slip Modal -->
    <?php if ($employeeData): ?>
        <div id="salary-modal" class="salary-modal">
            <div class="salary-modal-content">
                <h2>Salary Slip Details</h2>
                <div id="salary-details" class="salary-details">
                    <p><strong>Employee Name:</strong> <?= htmlspecialchars($employeeData['employee_name']) ?></p>
                    <p><strong>Employee ID:</strong> <?= htmlspecialchars($employeeData['id']) ?></p>
                    <p><strong>Payroll ID:</strong> <?= htmlspecialchars($employeeData['payroll_id']) ?></p>
                    <p><strong>Payroll Date From:</strong> <?= htmlspecialchars($employeeData['date_from']) ?></p>
                    <p><strong>Payroll Date To:</strong> <?= htmlspecialchars($employeeData['date_to']) ?></p>
                    <p><strong>Branch:</strong> <?= htmlspecialchars($employeeData['branch_name']) ?></p>
                    <p><strong>Manager:</strong> <?= htmlspecialchars($employeeData['department_manager']) ?></p>
                    <p><strong>Address:</strong> <?= htmlspecialchars($employeeData['department_address']) ?></p>
                    <p><strong>Position:</strong> <?= htmlspecialchars($employeeData['position']) ?></p>
                    <p><strong>Contact Number:</strong> <?= htmlspecialchars($employeeData['contact_num']) ?></p>
                    <p><strong>Monthly Salary:</strong> ₱<?= number_format($employeeData['monthly_salary'], 2) ?></p>
                    <p><strong>Total Deductions:</strong> ₱<?= number_format($employeeData['total_deductions'], 2) ?></p>

                    <h3>Deductions:</h3>
                    <ul>
                        <?php foreach ($deductionsData as $deduction): ?>
                            <li><strong><?= htmlspecialchars($deduction['deduction_name']) ?>:</strong> ₱<?= number_format($deduction['amount'], 2) ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p><strong>Net Salary:</strong> ₱<?= number_format($employeeData['net_salary'], 2) ?></p>
                </div>
                <div class="modal-buttons">
                    <form action="sendSalaryEmail.php" method="POST">
                        <input type="hidden" name="employee_email" value="<?= htmlspecialchars($employeeData['email']) ?>">
                        <button type="submit" class="send-btn">Send to Email</button>
                    </form>
                    <button class="close-btn" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>

        <script>
            // Show the modal once the page is loaded
            document.getElementById("salary-modal").style.display = "block";

            // Function to close the modal
            function closeModal() {
                document.getElementById("salary-modal").style.display = "none";
            }
        </script>
    <?php endif; ?>
</body>
</html>
