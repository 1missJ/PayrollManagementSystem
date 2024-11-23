<?php
// Connection to the database
$conn = new mysqli('localhost', 'root', '', 'payrollsystem');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

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
            background-color: #ffffff;
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
            padding-top: 80px;
            padding: 20px;
            width: calc(100% - 250px);
            height: calc(100vh - 60px);
            overflow-y: auto;
            background-color: #ffe4c4;
        }

        .welcome-box2 {
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

        .controls {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 50px;
        }

        .search-container {
            display: flex;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 20px;
            padding: 5px 10px;
            background-color: #ffffff;
        }

        .search-bar {
            border: none;
            outline: none;
            width: 200px;
            font-size: 1rem;
            background-color: transparent;
        }

        .search-btn {
            background: none;
            border: none;
            font-size: 1.2rem;
            cursor: pointer;
            color: #666;
        }

        .add-Employee-btn {
            padding: 8px 12px;
            font-size: 1rem;
            background-color: #ffa07a;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .Employee-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .Employee-table th, .Employee-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .Employee-table th {
            background-color: #f4a460;
            color: white;
            font-weight: bold;
        }

        .Employee-table td {
            background-color: #fff8f0;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .view {
            background-color: burlywood; 
            color: white;
            font-size: 1.2rem;
            padding: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .edit {
            background-color: #4CAF50; 
            color: white;
            font-size: 1.2rem;
            padding: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .delete {
            background-color: #f44336;
            color: white;
            font-size: 1.2rem;
            padding: 5px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
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

        /* Modal Styling */
        .modal {
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

/* Modal Content */
.modal-content {
    width: 80%; /* Adjust the width of the modal */
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

/* Modal Body */
.modal-body {
    display: flex;
    justify-content: space-between;
    gap: 20px; /* Space between the left and right columns */
}

/* Left Column (First Name, Last Name, etc.) */
.left-column {
    flex: 1;
}

/* Right Column (Middle Name, Address, etc.) */
.right-column {
    flex: 1;
}

/* Form Group (for each input field) */
.form-group {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between label and input */
    margin-bottom: 15px; /* Space between form groups */
}

/* Labels */
.modal-body label {
    font-weight: bold;
    font-size: 14px;
    color: #333;
    width: 150px; /* Fixed width for labels to ensure they align properly */
}

/* Input Fields */
.modal-body input {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 4px;
    width: 100%; /* Inputs take the remaining space */
    box-sizing: border-box;
}

/* Date Inputs */
.modal-body input[type="date"] {
    width: 100%;
    padding: 10px;
}

/* Modal Footer */
.modal-footer {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    margin-top: 20px;
}

.modal-footer .save-btn,
.modal-footer .cancel-btn {
    padding: 10px 20px;
    font-size: 14px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.modal-footer .save-btn {
    background-color: #4CAF50;
    color: white;
}

.modal-footer .cancel-btn {
    background-color: #f44336;
    color: white;
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
    <div class="header">
        <div class="header-left">
            <img src="logo.png" alt="Company Logo" class="logo-img">
        </div>
        <div class="header-right">
            <span class="admin-text">Admin</span>
            <a href="logout.php" class="logout-icon">🚪</a>
        </div>
    </div>

    <div class="sidebar">
        <ul>
             <li><a href="home.php"><i class="icon">🏠</i> Home</a></li>
            <li><a href="payrollList.php"><i class="icon">💸</i> Payroll</a></li>
            <li class="active"><a href="employee.php"><i class="icon">👥</i> Employees</a></li>
            <li><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deduction.php"><i class="icon">➖</i> Deduction</a></li>
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

    </script>
</body>
</html>
