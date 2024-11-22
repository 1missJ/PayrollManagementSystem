<?php
include('db_connection.php'); // Including the DB connection file

// Fetch employees
$sql = "SELECT * FROM employees";
$result = $conn->query($sql);

// Add employee
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['saveEmployee'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $department = $_POST['department'];
    $position = $_POST['position'];
    $salary = $_POST['salary'];
    $dateJoined = $_POST['dateJoined'];

    // Insert employee data into the table
    $sql = "INSERT INTO employees (first_name, last_name, department, position, salary, date_joined)
            VALUES ('$firstName', '$lastName', '$department', '$position', '$salary', '$dateJoined')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Employee added successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
    }
}

// Delete employee
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM employees WHERE employee_id = $delete_id";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Employee deleted successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
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

.modal-content {
    background-color: #fff;
    padding: 20px;
    width: 700px;
    max-height: 80vh;  /* Limit the max height of the modal */
    border-radius: 5px;
    text-align: center;
    box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}

.modal-header {
    font-size: 1.5rem;
    margin-bottom: 10px;
    font-weight: bold;
}

.modal-body {
    flex-grow: 1;
    overflow-y: auto;  /* Enable vertical scrolling */
    padding: 10px;
}

.modal-body input, .modal-body select {
    width: 70%;
    padding: 10px;
    margin: 10px 0;
    border-radius: 5px;
    border: 1px solid #ccc;
    font-size: 1rem;
}

.modal-footer {
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
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Department</th>
                    <th>Position</th>
                    <th>Salary</th>
                    <th>Date Joined</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0) { 
                    while($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['employee_id']; ?></td>
                            <td><?php echo $row['first_name']; ?></td>
                            <td><?php echo $row['last_name']; ?></td>
                            <td><?php echo $row['department']; ?></td>
                            <td><?php echo $row['position']; ?></td>
                            <td><?php echo number_format($row['salary'], 2); ?></td>
                            <td><?php echo $row['date_joined']; ?></td>
                            <td class="action-buttons">
                                <button class="view"><img src="eye.png" alt="eye"></button>
                                <button class="edit">✏️</button>
                                <a href="?delete_id=<?php echo $row['employee_id']; ?>" class="delete" onclick="showDeleteConfirmation(event)"><img src="delete.png" alt="delete" style="height: 20px; width: 25px;"></a>
                            </td>
                        </tr>
                <?php } } ?>
            </tbody>
        </table>
    </main>

    <!-- Modal for adding new employee -->
    <div id="newEmployeeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">New Employee</div>
            <div style="border-top: 2px solid black; margin: 10px 0;"></div>
            <div class="modal-body">
                <form action="" method="POST">
                    <label for="firstName">First Name:</label>
                    <input type="text" name="firstName" required><br>

                    <label for="lastName">Last Name:</label>
                    <input type="text" name="lastName" required><br>

                    <label for="department">Department:</label>
                    <input type="text" name="department" required><br>

                    <label for="position">Position:</label>
                    <input type="text" name="position" required><br>

                    <label for="salary">Salary:</label>
                    <input type="text" name="salary" required><br>

                    <label for="dateJoined">Date Joined:</label>
                    <input type="date" name="dateJoined" required><br>

                    <button class="save-btn" name="saveEmployee">Save</button>
                    <button class="cancel-btn" type="button" onclick="closeModal()">Cancel</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Footer, Modals, and Scripts (unchanged) -->
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
        window.onclick = function(event) {
            const modal = document.getElementById('newEmployeeModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        function saveEmployee() {
            alert("Employee saved!");
            closeModal();
        }
    </script>

<div class="logout-overlay" id="logoutOverlay">
    <div class="logout-content">
        <div class="logout-header" style="padding: 20px;">Confirmation</div>
        <div style="border-top: 2px solid black; margin: 10px 0;"></div>
        <p style="padding: 30px;">Are you sure you want to log out?</p>
        <div style="border-top: 2px solid black; margin: 10px 0;"></div>
        <div class="logout-footer">
            <button class="cancel-btn" onclick="closeLogoutConfirmation()">No</button>
            <button class="confirm-btn" onclick="confirmLogout()">Yes</button>
        </div>
    </div>
</div>

<script>
      
    function showLogoutConfirmation(event) {
        event.preventDefault(); 
        document.getElementById("logoutOverlay").style.display = "flex";
    }


    function closeLogoutConfirmation() {
        document.getElementById("logoutOverlay").style.display = "none";
    }

 
    function confirmLogout() {
        window.location.href = "login.php"; 
    }
</script>



<div class="delete-overlay" id="deleteOverlay">
    <div class="delete-content">
        <div class="delete-header" style="padding: 20px;">Delete Confirmation</div>
        <div style="border-top: 2px solid black; margin: 10px 0;"></div>
        <p style="padding: 10px; ">Are you sure you want to delete this payroll?</p>
        <div style="border-top: 2px solid black; margin: 10px 0;"></div>
        <div class="delete-footer">
            <button class="cancelll-btn" onclick="closeDeleteConfirmation()">No</button>
            <button class="confirm-btn" onclick="confirmDelete()">Yes</button>
        </div>
    </div>
</div>
<script>
// Function to show the delete confirmation modal
function showDeleteConfirmation(event) {
    event.preventDefault(); // Prevents any default action (like a form submit or page redirect)
    document.getElementById("deleteOverlay").style.display = "flex"; // Show the delete confirmation modal
}

// Function to close the delete confirmation modal
function closeDeleteConfirmation() {
    document.getElementById("deleteOverlay").style.display = "none"; // Hide the delete confirmation modal
}

// Function to confirm the deletion
function confirmDelete() {
    alert("Payroll deleted!"); // Here you can handle actual deletion (e.g., make an AJAX request)
    closeDeleteConfirmation(); // Close the modal after confirmation
}


</script>


</body>
</html>

<?php $conn->close(); ?>
