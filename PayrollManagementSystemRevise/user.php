<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="user.css">
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
            <li><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deductionList.php"><i class="icon">➖</i> Deduction</a></li>
            <li><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li class="active"><a href="user.php"><i class="icon">👤</i> User</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="welcome-box2">
            User
        </div>

        <table class="User-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>UserName</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="action-buttons">
                        <button class="edit">✏️</button>
                        <a href="#" class="delete" onclick="showDeleteConfirmation(event)"><img src="delete.png" alt="delete" style="height: 20px; width: 25px;"></a>
                        
                    </td>
                </tr>
            </tbody>
        </table>
    </main>

    <script>
        function confirmLogout(event) {
            event.preventDefault(); 
            if (confirm("Are you sure you want to log out?")) {
                window.location.href = "logout.php"; 
            }
        }
    </script>

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
        // Show the logout confirmation
        function showLogoutConfirmation(event) {
            event.preventDefault(); // Prevents the default action
            document.getElementById("logoutOverlay").style.display = "flex";
        }

        // Hide the logout confirmation
        function closeLogoutConfirmation() {
            document.getElementById("logoutOverlay").style.display = "none";
        }

        // Confirm the logout
        function confirmLogout() {
            window.location.href = "login.html"; // Redirect to logout page
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