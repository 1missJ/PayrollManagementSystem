<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
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
            background-color: #fff3e0; 
            border-top: 1px solid #ccc;
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

        .User-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .User-table th, .User-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }

        .User-table th {
            background-color: #f4a460;
            color: white;
            font-weight: bold;
        }

        .User-table td {
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
            width: 400px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            font-size: 1.5rem;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .modal-body input, .modal-body select {
            width: 100%;
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
            <li><a href="employee.php"><i class="icon">👥</i> Employees</a></li>
            <li><a href="branch.php"><i class="icon">🏢</i> Branch</a></li>
            <li><a href="deduction.php"><i class="icon">➖</i> Deduction</a></li>
            <li><a href="salarySlip.php"><i class="icon">📄</i> Salary Slip</a></li>
            <li class="active"><a href="user.php"><i class="icon">👤</i> User</a></li>
        </ul>
    </div>

    <main class="content">
        <div class="welcome-box2">
            User LIST
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
                <button class="confirm-btn" onclick="confirmLogout()">Yes</button>
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