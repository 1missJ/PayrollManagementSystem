<?php
session_start();

// Redirect if the admin is not logged in
if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php'; // Include database connection

// Enable detailed error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetching salary slip data for all employees
$salaryDetailsQuery = "
    SELECT 
        e.id, 
        CONCAT(e.first_name, ' ', e.last_name) AS employee_name, 
        e.contact_num, 
        e.position, 
        e.monthly_salary, 
        (e.monthly_salary - COALESCE(SUM(d.amount), 0)) AS net_salary, 
        b.branch_name, 
        b.department_manager, 
        b.department_address,
        COALESCE(SUM(d.amount), 0) AS total_deductions
    FROM employees e
    LEFT JOIN branch_employee be ON e.id = be.employee_id
    LEFT JOIN branch b ON be.branch_id = b.id
    LEFT JOIN deduction d ON e.id = d.employee_id
    GROUP BY e.id
";
$result = $conn->query($salaryDetailsQuery);

// Error handling for query execution
if (!$result) {
    die("Query Failed: " . $conn->error);
}

// Fetch employee salary slip details (if employee_id is passed)
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
            b.branch_name, 
            b.department_manager, 
            b.department_address,
            COALESCE(SUM(d.amount), 0) AS total_deductions
        FROM employees e
        LEFT JOIN branch_employee be ON e.id = be.employee_id
        LEFT JOIN branch b ON be.branch_id = b.id
        LEFT JOIN deduction d ON e.id = d.employee_id
        WHERE e.id = ?
        GROUP BY e.id
    ";

    $stmt = $conn->prepare($salaryDetailsQuery);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();
    $result = $stmt->get_result();

    // If there's data, return it as JSON
    if ($result->num_rows > 0) {
        $employeeData = $result->fetch_assoc();

        // Fetch deductions for this employee
        $deductionsQuery = "
            SELECT cd.name, d.amount
            FROM deduction d
            LEFT JOIN custom_deductions cd ON d.deduction_id = cd.id
            WHERE d.employee_id = ?
        ";
        $stmt = $conn->prepare($deductionsQuery);
        $stmt->bind_param("i", $employeeId);
        $stmt->execute();
        $deductionsResult = $stmt->get_result();

        $deductions = [];
        while ($deduction = $deductionsResult->fetch_assoc()) {
            $deductions[] = $deduction;
        }

        // Add deductions to employee data
        $employeeData['deductions'] = $deductions;

        header('Content-Type: application/json');
        echo json_encode($employeeData);
        exit;
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'No data found for this employee']);
        exit;
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
        /* Modal Styles */
        .salary-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 2;
        }

        .salary-modal-content {
            background-color: #fff;
            padding: 20px;
            width: 600px;
            border-radius: 5px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .salary-modal-content h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .salary-modal-content p {
            margin: 10px 0;
        }

        /* Buttons Section */
        .modal-buttons {
            display: flex;
            justify-content: flex-end; 
            gap: 10px; 
            margin-top: 20px;
        }

        .salary-modal-content button {
            padding: 10px 20px;
            background-color: #ffa07a;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px; 
            font-size: 1rem;
            transition: background-color 0.3s ease; 
        }

        .salary-modal-content button:hover {
            background-color: #ff7f50;
        }

        #printable-area {
            border: 1px solid #000;
            padding: 50px;
            margin: 10px 0;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        #printable-area p {
            line-height: 1.5;
        }

        @media print {
            .salary-modal-content {
                box-shadow: none;
                padding: 0;
                width: auto;
            }

            .modal-buttons {
                display: none; 
            }

            #printable-area {
                border: none;
                padding: 0;
                margin: 0;
            }
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
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['employee_name']) ?></td>
                        <td>₱<?= number_format($row['monthly_salary'], 2) ?></td>
                        <td>₱<?= number_format($row['total_deductions'], 2) ?></td>
                        <td>₱<?= number_format($row['net_salary'], 2) ?></td>
                        <td>
                            <button class="view" data-employee-id="<?= $row['id'] ?>">
                                <img src="./assets/images/eye.png" alt="View" style="height: 20px; width: 25px; padding-top: 5px">
                            </button>
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

<div id="salary-modal" class="salary-modal">
    <div class="salary-modal-content">
        <h2>Salary Slip Details</h2>
        <div id="printable-area">
            <p><strong>Employee Name:</strong> <span id="modal-employee-name"></span></p>
            <p><strong>Employee ID:</strong> <span id="modal-employee-id"></span></p>
            <p><strong>Branch:</strong> <span id="modal-branch"></span></p>
            <p><strong>Manager:</strong> <span id="modal-manager"></span></p>
            <p><strong>Address:</strong> <span id="modal-address"></span></p>
            <p><strong>Position:</strong> <span id="modal-position"></span></p>
            <p><strong>Contact Number:</strong> <span id="modal-contact"></span></p>
            <p><strong>Monthly Salary:</strong> <span id="modal-monthly-salary"></span></p>
            <p><strong>Total Deductions:</strong> <span id="modal-total-deductions"></span></p>
            <p><strong>Net Salary:</strong> <span id="modal-net-salary"></span></p>
            <p><strong>Deductions:</strong></p>
            <ul id="modal-deductions"></ul>
        </div>
        <div class="modal-buttons">
            <button id="printBtn" onclick="printSlip()">Print Salary Slip</button>
            <button onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
 document.addEventListener('DOMContentLoaded', function() {
    // Handle click event to view salary slip details
    document.querySelectorAll('.view').forEach(button => {
        button.addEventListener('click', function() {
            const employeeId = this.getAttribute('data-employee-id');
            
            // Fetch the data for the specific employee
            fetch(`salarySlip.php?employee_id=${employeeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        // Populate the modal with employee data
                        document.getElementById('modal-employee-name').textContent = data.employee_name;
                        document.getElementById('modal-employee-id').textContent = data.id;
                        document.getElementById('modal-branch').textContent = data.branch_name;
                        document.getElementById('modal-manager').textContent = data.department_manager;
                        document.getElementById('modal-address').textContent = data.department_address;
                        document.getElementById('modal-position').textContent = data.position;
                        document.getElementById('modal-contact').textContent = data.contact_num;
                        document.getElementById('modal-monthly-salary').textContent = '₱' + parseFloat(data.monthly_salary).toFixed(2);
                        document.getElementById('modal-total-deductions').textContent = '₱' + parseFloat(data.total_deductions).toFixed(2);
                        document.getElementById('modal-net-salary').textContent = '₱' + parseFloat(data.net_salary).toFixed(2);
                        
                        // Handle custom deductions if present
                        const deductionsList = document.getElementById('modal-deductions');
                        deductionsList.innerHTML = ''; // Clear any previous deductions
                        if (data.deductions && data.deductions.length > 0) {
                            data.deductions.forEach(deduction => {
                                const listItem = document.createElement('li');
                                listItem.textContent = `${deduction.name}: ₱${parseFloat(deduction.amount).toFixed(2)}`;
                                deductionsList.appendChild(listItem);
                            });
                        }

                        // Make the modal visible
                        document.getElementById('salary-modal').style.display = 'flex';
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                    alert('An error occurred while fetching the data.');
                });
        });
    });

    // Close modal when the close button is clicked
    document.querySelector('.modal-buttons button:last-child').addEventListener('click', function() {
        document.getElementById('salary-modal').style.display = 'none';
    });

    // Print salary slip when the print button is clicked
    document.querySelector('.modal-buttons button:first-child').addEventListener('click', function() {
        const printArea = document.getElementById('printable-area');
        const printWindow = window.open('', '', 'height=600,width=800');
        printWindow.document.write('<html><head><title>Salary Slip</title></head><body>');
        printWindow.document.write(printArea.outerHTML);
        printWindow.document.write('</body></html>');
        printWindow.document.close();
        printWindow.print();
    });
});
   </script>

</body>
</html>
