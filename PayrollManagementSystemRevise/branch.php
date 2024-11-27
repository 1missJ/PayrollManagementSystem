<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php"); 
    exit;
}

include 'connection.php'; // Database connection

// Save branch to database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_branch'])) {
    $manager = $_POST['department_manager'];
    $address = $_POST['department_address'];

    $query = "INSERT INTO branch (department_manager, department_address) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $manager, $address);

    if ($stmt->execute()) {
        echo "<script>alert('Branch added successfully!'); window.location.href='branch.php';</script>";
    } else {
        echo "<script>alert('Error adding branch: " . $conn->error . "');</script>";
    }

    if (isset($_GET['delete'])) {
    $branch_id = (int)$_GET['delete'];

    // Delete associated records (if necessary)
    $delete_query = "DELETE FROM branch WHERE id = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("i", $branch_id);
    
    if ($stmt->execute()) {
        echo "<script>alert('Branch deleted successfully!'); window.location.href='branch.php';</script>";
    } else {
        echo "<script>alert('Error deleting branch: " . $conn->error . "');</script>";
    }
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch List</title>
    <link rel="stylesheet" href="branch.css">
    
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
            Branch
        </div>
       <div class="Branch-form">
    <h3 style="text-align: center; font-weight: bold;">Add Branch</h3>
    <div style="border-top: 2px solid black; margin: 10px 0;"></div>
    <form method="POST" action="branch.php">
        <label for="department-manager">Department Manager</label>
        <input type="text" id="department-manager" name="department_manager" required>
        <label for="department-address">Department Address</label>
        <input type="text" id="department-address" name="department_address" required>
        <div style="border-top: 2px solid black; margin: 20px 0;"></div>
        <div class="form-buttons">
           <button type="button" class="cancel-btn" onclick="clearFields()">CANCEL</button>

<script>
    function clearFields() {
        document.getElementById('department-manager').value = '';
        document.getElementById('department-address').value = '';
    }
</script>

            <button type="submit" name="save_branch" class="save-btn">SAVE</button>
        </div>
    </form>
</div>


        <div class="controls">
            <div class="search-container">
                <input type="text" placeholder="Search" class="search-bar">
                <button class="search-btn">🔍</button>
            </div>
          

        </div>

        <table class="Branch-table">
    <thead>
        <tr>
            <th>Branch Information</th>
            <th>Action</th>
        </tr>
    </thead>
   
   <tbody>
    <?php
    $query = "SELECT id, department_manager, department_address FROM branch";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>Manager: {$row['department_manager']}<br>Address: {$row['department_address']}</td>
                <td class='action-buttons'>
                    <!-- View Button with Eye Image -->
                    <a href='view_branch_employee.php?view={$row['id']}' class='view'>
                        <img src='eye.png' alt='view' style='height: 20px; width: 20px;'>
                    </a>

                    <!-- Delete Button with Delete Image -->
                    <a href='branch.php?delete={$row['id']}' class='delete' onclick='return confirm(\"Are you sure you want to delete this branch?\");'>
                        <img src='delete.png' alt='delete' style='height: 20px; width: 25px;'>
                    </a>

                    <!-- Add Employee Button -->
                    <button class='add-Employee-btn'>
                        <a href='add_employees_to_branch.php?branch_id={$row['id']}'>+ Add Employee</a>
                    </button>
                </td>
              </tr>";
    }
    ?>
</tbody>


</table>

    </main>

    

        </form>
    </div>
</div>

    <footer class="footer">
            © 2024 Payroll Management System. All rights reserved.
    </footer>
    
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

    <script>
        function showModal() {
            document.getElementById('newEmployeeModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('newEmployeeModal').style.display = 'none';
        }

        function closeViewModal() {
    window.location.href = 'branch.php'; // Redirect to main branch page
}

        function closeEditModal() {
            window.location.href = '#'; // Redirect to main # page
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
