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
}

// Delete branch and its dependent records
if (isset($_GET['delete'])) {
    $branch_id = (int)$_GET['delete'];

    // Delete associated records in branch_employee table first (if not using cascading delete)
    $delete_branch_employee = "DELETE FROM branch_employee WHERE branch_id = ?";
    $stmt1 = $conn->prepare($delete_branch_employee);
    $stmt1->bind_param("i", $branch_id);

    if ($stmt1->execute()) {
        // Then, delete the branch from the branch table
        $delete_branch = "DELETE FROM branch WHERE id = ?";
        $stmt2 = $conn->prepare($delete_branch);
        $stmt2->bind_param("i", $branch_id);

        if ($stmt2->execute()) {
            echo "<script>alert('Branch deleted successfully!'); window.location.href='branch.php';</script>";
        } else {
            echo "<script>alert('Error deleting branch: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error deleting associated records in branch_employee: " . $conn->error . "');</script>";
    }
}

// Search functionality
$search_query = '';
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = $_GET['search'];
    $query = "SELECT id, department_manager, department_address FROM branch WHERE department_manager LIKE ? OR department_address LIKE ?";
    $stmt = $conn->prepare($query);
    $search_term = '%' . $search_query . '%';
    $stmt->bind_param("ss", $search_term, $search_term);
} else {
    $query = "SELECT id, department_manager, department_address FROM branch";
    $stmt = $conn->prepare($query);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branch List</title>
    <link rel="stylesheet" href="./assets/css/branch.css">
</head>
<body>
    <!-- header -->
    <?php include_once("./includes/header.php"); ?>
     
    <!-- sidebar -->
    <?php include_once("./includes/sidebar.php"); ?>

    <main class="content">
        <div class="Branch-box">
            BRANCH
        </div>
       <div class="Branch-form">
        <h3 style="text-align: center; font-weight: bold;">Add Branch</h3>
        <div style="border-top: 2px solid black; margin: 10px 0;"></div>
        <form method="POST" action="branch.php">
            <label for="department-manager">Branch Manager</label>
            <input type="text" id="department-manager" name="department_manager" required placeholder="Enter Branch Manager">
            <label for="department-address">Branch Address</label>
            <input type="text" id="department-address" name="department_address" required placeholder="Enter Branch Address">
            <div style="border-top: 2px solid black; margin: 20px 0;"></div>
            <div class="form-buttons">
                <button type="button" class="cancel-btn" onclick="clearFields()">CANCEL</button>
                
                <script>
                    function clearFields() {
                        // Clear the values of the inputs when cancel button is clicked
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
            <form method="GET" action="branch.php">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" placeholder="Search by Manager or Address" class="search-bar">
                <button type="submit" class="search-btn">🔍</button>
            </form>
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
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>Manager: {$row['department_manager']}<br>Address: {$row['department_address']}</td>
                    <td class='action-buttons'>
                        <!-- View Button with Eye Image -->
                        <a href='view_branch_employee.php?view={$row['id']}' class='view'>
                            <img src='./assets/images/eye.png' alt='view' style='height: 25px; width: 25px; padding-top: 5px'>
                        </a>

                        <!-- Delete Button -->
                        <a href='branch.php?delete={$row['id']}' class='delete' style='padding-top: 10px' onclick='return confirm(\"Are you sure you want to delete this branch?\");'>
                            <img src='./assets/images/delete.png' alt='delete' style='height: 20px; width: 25px;'>
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

 <!-- footer -->
 <?php include_once("./includes/footer.php"); ?>
    
    <?php include_once("./modal/logout-modal.php"); ?>

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
    </script>
</body>
</html>
