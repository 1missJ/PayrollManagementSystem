<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php"); 
    exit;
}
// Connection to the database
include_once("connection.php");

// Handle Add Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_employee'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $contact_num = $_POST['contact_num'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $civil_status = $_POST['civil_status'];
    $date_hired = $_POST['date_hired'];
    $monthly_salary = $_POST['monthly_salary'];
    $dob = $_POST['dob'];

    $sql = "INSERT INTO employees (first_name, middle_name, last_name, address, contact_num, position, email, gender, civil_status, date_hired, monthly_salary, dob) 
            VALUES ('$first_name', '$middle_name', '$last_name', '$address', '$contact_num', '$position', '$email', '$gender', '$civil_status', '$date_hired', '$monthly_salary', '$dob')";
    $conn->query($sql);
    header('Location: employee.php');
    exit();
}

// Handle Delete Employee
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // Delete associated deductions first
    $conn->query("DELETE FROM deduction WHERE employee_id = $id");
    
    // Now delete the employee
    $conn->query("DELETE FROM employees WHERE id = $id");
    
    header('Location: employee.php');
    exit();
}


// Fetch Employee Details for View
if (isset($_GET['view'])) {
    $id = $_GET['view'];
    $view_result = $conn->query("SELECT * FROM employees WHERE id = $id");
    $employee = $view_result->fetch_assoc(); // Fetch as associative array
}

// Fetch Employee Details for Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $edit_result = $conn->query("SELECT * FROM employees WHERE id = $id");
    $employee_edit = $edit_result->fetch_assoc(); // Fetch as associative array
}

// Handle Update Employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_employee'])) {
    $id = $_POST['id'];
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $address = $_POST['address'];
    $contact_num = $_POST['contact_num'];
    $position = $_POST['position'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $civil_status = $_POST['civil_status'];
    $date_hired = $_POST['date_hired'];
    $monthly_salary = $_POST['monthly_salary'];
    $dob = $_POST['dob'];

    $sql = "UPDATE employees SET first_name='$first_name', middle_name='$middle_name', last_name='$last_name', address='$address', 
            contact_num='$contact_num', position='$position', email='$email', gender='$gender', civil_status='$civil_status', 
            date_hired='$date_hired', monthly_salary='$monthly_salary', dob='$dob' WHERE id=$id";

    $conn->query($sql);
    header('Location: employee.php');
    exit();
}




// Fetch all employees
$result = $conn->query("SELECT * FROM employees");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
    <link rel="stylesheet" href="employee.css">
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
            <li class="active"><a href="employee.php"><i class="icon">👥</i> Employees</a></li>
            <li><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deductionList.php"><i class="icon">➖</i> Deduction</a></li>
            <li><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li><a href="user.php"><i class="icon">👤</i> User</a></li>

        </ul>
    </div>

    <main class="content">
        <div class="welcome-box2">
            Employee LIST
        </div>

        <div class="controls">
            <div class="search-container">
                <input type="text" placeholder="Search" class="search-bar">
                <button class="search-btn">🔍</button>
            </div>
            <button class="add-Employee-btn" onclick="showModal()">+ Add Employee</button>
        </div>

        <table class="Employee-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>FirstName</th>
                    <th>MiddleName</th>
                    <th>LastName</th>
                    <th>Address</th>
                    <th>Contact Num.</th>
                    <th>Position</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= $row['first_name'] ?></td>
                    <td><?= $row['middle_name'] ?></td>
                    <td><?= $row['last_name'] ?></td>
                    <td><?= $row['address'] ?></td>
                    <td><?= $row['contact_num'] ?></td>
                    <td><?= $row['position'] ?></td>
                   <td class="action-buttons">
    <!-- View Button with Eye Image -->
    <a href="employee.php?view=<?= $row['id'] ?>" class="view">
        <img src="eye.png" alt="view" style="height: 20px; width: 20px;">
    </a>
    
    <!-- Edit Button with Green Background -->
    <button class="edit" onclick="window.location.href='employee.php?edit=<?= $row['id'] ?>';" style="background-color: #4CAF50; border: none; color: white; padding: 5px 5px; border-radius: 4px;">
        ✏️
    </button>
    
    <!-- Delete Button with Delete Image -->
    <a href="employee.php?delete=<?= $row['id'] ?>" class="delete" onclick="return confirm('Are you sure you want to delete this employee?');">
        <img src="delete.png" alt="delete" style="height: 20px; width: 25px;">
    </a>
</td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>

   <div id="newEmployeeModal" class="modal">
    <div class="modal-content">
        <form method="POST" action="employee.php">
            <div class="modal-header">
                <h3>New Employee</h3>
            </div>
            <div class="modal-body">
                <div class="left-column">
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="form-group">
                        <label for="middle_name">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" placeholder="Middle Name" required>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" placeholder="Address" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_num">Contact Number:</label>
                        <input type="text" id="contact_num" name="contact_num" placeholder="Contact Number" required>
                    </div>
                    <div class="form-group">
                        <label for="position">Position:</label>
                        <input type="text" id="position" name="position" placeholder="Position" required>
                    </div>
                </div>
                <div class="right-column">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="civil_status">Civil Status:</label>
                        <select id="civil_status" name="civil_status" required>
                            <option value="" disabled selected>Civil Status</option>
                            <option value="Single">Single</option>
                            <option value="Married">Married</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" required>
                    </div>
                    <div class="form-group">
                        <label for="date_hired">Date Hired:</label>
                        <input type="date" id="date_hired" name="date_hired" required>
                    </div>
                    <div class="form-group">
                        <label for="monthly_salary">Monthly Salary:</label>
                        <input type="text" id="monthly_salary" name="monthly_salary" placeholder="Monthly Salary" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" name="save_employee" class="save-btn">Save</button>
                <button type="button" class="cancel-btn" onclick="closeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div id="viewEmployeeModal" class="modal" style="<?= isset($_GET['view']) ? 'display: flex;' : 'display: none;' ?>">
    <div class="modal-content">
        <div class="modal-header" style="display: flex; justify-content: center; width: 100%;">
            <h3>Employee Details</h3>
        </div>
        <div class="modal-body" style="margin-top: 20px;"> <!-- Added margin-top for spacing -->
            <?php if (isset($employee)): ?>
                <div class="left-column">
                    <div class="form-group">
                        <label>Name:</label>
                        <p><?= $employee['first_name'] . " " . $employee['middle_name'] . " " . $employee['last_name'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Address:</label>
                        <p><?= $employee['address'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Contact Number:</label>
                        <p><?= $employee['contact_num'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Position:</label>
                        <p><?= $employee['position'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <p><?= $employee['email'] ?></p>
                    </div>
                </div>
                <div class="right-column">
                    <div class="form-group">
                        <label>Gender:</label>
                        <p><?= $employee['gender'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Civil Status:</label>
                        <p><?= $employee['civil_status'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Date Hired:</label>
                        <p><?= $employee['date_hired'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Monthly Salary:</label>
                        <p><?= $employee['monthly_salary'] ?></p>
                    </div>
                    <div class="form-group">
                        <label>Date of Birth:</label>
                        <p><?= $employee['dob'] ?></p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="modal-footer">
            <button type="button" class="cancel-btn" onclick="closeViewModal()">Close</button>
        </div>
    </div>
</div>


<div id="editEmployeeModal" class="modal" style="<?= isset($_GET['edit']) ? 'display: flex;' : 'display: none;' ?>">
    <div class="modal-content">
        <form method="POST" action="employee.php">
            <input type="hidden" name="id" value="<?= $employee_edit['id'] ?? '' ?>">

            <!-- Modal Header -->
            <div class="modal-header">Edit Employee</div>

            <!-- Modal Body -->
            <div class="modal-body">
                <div class="left-column">
                    <!-- First Name -->
                    <div class="form-group">
                        <label for="first_name">First Name:</label>
                        <input type="text" id="first_name" name="first_name" value="<?= $employee_edit['first_name'] ?? '' ?>" required>
                    </div>

                    <!-- Last Name -->
                    <div class="form-group">
                        <label for="last_name">Last Name:</label>
                        <input type="text" id="last_name" name="last_name" value="<?= $employee_edit['last_name'] ?? '' ?>" required>
                    </div>

                    <!-- Contact Number -->
                    <div class="form-group">
                        <label for="contact_num">Contact Number:</label>
                        <input type="text" id="contact_num" name="contact_num" value="<?= $employee_edit['contact_num'] ?? '' ?>" required>
                    </div>

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?= $employee_edit['email'] ?? '' ?>" required>
                    </div>

                    <!-- Civil Status -->
                    <div class="form-group">
                        <label for="civil_status">Civil Status:</label>
                        <input type="text" id="civil_status" name="civil_status" value="<?= $employee_edit['civil_status'] ?? '' ?>" required>
                    </div>

                    <!-- Monthly Salary -->
                    <div class="form-group">
                        <label for="monthly_salary">Monthly Salary:</label>
                        <input type="text" id="monthly_salary" name="monthly_salary" value="<?= $employee_edit['monthly_salary'] ?? '' ?>" required>
                    </div>

                    <!-- Date Hired -->
                    <div class="form-group">
                        <label for="date_hired">Date Hired:</label>
                        <input type="date" id="date_hired" name="date_hired" value="<?= $employee_edit['date_hired'] ?? '' ?>" required>
                    </div>
                </div>

                <div class="right-column">
                    <!-- Middle Name -->
                    <div class="form-group">
                        <label for="middle_name">Middle Name:</label>
                        <input type="text" id="middle_name" name="middle_name" value="<?= $employee_edit['middle_name'] ?? '' ?>" required>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?= $employee_edit['address'] ?? '' ?>" required>
                    </div>

                    <!-- Position -->
                    <div class="form-group">
                        <label for="position">Position:</label>
                        <input type="text" id="position" name="position" value="<?= $employee_edit['position'] ?? '' ?>" required>
                    </div>

                    <!-- Gender -->
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <input type="text" id="gender" name="gender" value="<?= $employee_edit['gender'] ?? '' ?>" required>
                    </div>

                    <!-- Date of Birth -->
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" id="dob" name="dob" value="<?= $employee_edit['dob'] ?? '' ?>" required>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="submit" name="update_employee" class="save-btn">Save Changes</button>
                <button type="button" class="cancel-btn" onclick="closeEditModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>


    <div class="logout-overlay" id="logoutOverlay">
        <div class="logout-content">
            <div class="logout-header" style="padding: 20px;">Confirmation</div>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <p style="padding: 30px;">Are you sure you want to log out?</p>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <div class="logout-footer">
                <button class="cancel-btn" onclick="closeLogoutConfirmation()">No</button>
                <a href="logout.php?logout=confirm"><button class="confirm-btn">Yes</button></a>
            </div>
        </div>
    </div>

    <footer class="footer">
        © 2024 Payroll Management System. All rights reserved.
    </footer>

    <script>
        function showModal() {
            document.getElementById('newEmployeeModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('newEmployeeModal').style.display = 'none';
        }

        function closeViewModal() {
    window.location.href = 'employee.php'; // Redirect to main employee page
}

        function closeEditModal() {
            window.location.href = 'employee.php'; // Redirect to main employee page
        }
        function showLogoutConfirmation(event) {
            event.preventDefault();
            document.getElementById("logoutOverlay").style.display = "flex";
        }

        function closeLogoutConfirmation() {
            document.getElementById("logoutOverlay").style.display = "none";
        }
        function showLogoutModal() {
            document.getElementById('logoutOverlay').style.display = 'flex';
        }
        function closeLogoutConfirmation() {
            document.getElementById('logoutOverlay').style.display = 'none';
        }

    </script>
</body>
</html>
