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
    <link rel="stylesheet" href="./assets/css/user.css">
</head>
<body>
    
<!-- header -->
    <?php include_once("./includes/header.php"); ?>
<!-- sidebar -->
    <?php include_once("./includes/sidebar.php"); ?>

    <main class="content">
        <div class="welcome-box2">
            Profile
        </div>

        <!-- <table class="User-table">
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
                        <a href="#" class="delete" onclick="showDeleteConfirmation(event)"><img src="./assets/images/delete.png" alt="delete" style="height: 20px; width: 25px;"></a>
                        
                    </td>
                </tr>
            </tbody>
        </table> -->
    </main>
 <!-- footer -->
 <?php include_once("./includes/footer.php"); ?>

</body>
</html>