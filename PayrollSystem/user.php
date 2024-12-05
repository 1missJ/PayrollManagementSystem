<?php
session_start();

if (!isset($_SESSION['Admin_User'])) {
    header("Location: login.php");
    exit;
}

include_once("connection.php");
$username = $_SESSION['Admin_User'];
$error_message = '';
$success_message = '';

// Fetch user data
$stmt = $conn->prepare("SELECT username, password, security_answer1, security_answer2, security_answer3 FROM super_user WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($currentUsername, $currentPassword, $security1, $security2, $security3);
$stmt->fetch();
$stmt->close();

// Handle form submission for updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username']);
    $newPassword = trim($_POST['password']);
    $newAnswer1 = trim($_POST['security_answer1']);
    $newAnswer2 = trim($_POST['security_answer2']);
    $newAnswer3 = trim($_POST['security_answer3']);

    // Update user data
    $updateStmt = $conn->prepare("UPDATE super_user SET username = ?, password = ?, security_answer1 = ?, security_answer2 = ?, security_answer3 = ? WHERE username = ?");
    $updateStmt->bind_param("ssssss", $newUsername, $newPassword, $newAnswer1, $newAnswer2, $newAnswer3, $username);

    if ($updateStmt->execute()) {
        $_SESSION['Admin_User'] = $newUsername; // Update session username
        $success_message = "Profile updated successfully.";
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }

    $updateStmt->close();
    $username = $newUsername; // Refresh username for display
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="./assets/css/user.css">
    <style>
        .profile-container {
            background-color: #ffffff;
            margin: 20px auto;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }
        .profile-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }
        .profile-container form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .profile-container label {
            font-weight: bold;
            color: #555;
        }
        .profile-container input {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            width: 100%;
        }
        .profile-container button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            padding: 10px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .profile-container button:hover {
            background-color: #0056b3;
        }
        .profile-container .message {
            font-size: 14px;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
        }
        .profile-container .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .profile-container .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
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

    <div class="profile-container">
        <h2>Update Profile</h2>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="username">New Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($currentUsername); ?>" required>
            
            <label for="password">New Password:</label>
            <input type="password" id="password" name="password" value="<?php echo htmlspecialchars($currentPassword); ?>" required>
            
            <label for="security_answer1">Answer 1 (What is your Middle Name?):</label>
            <input type="text" id="security_answer1" name="security_answer1" value="<?php echo htmlspecialchars($security1); ?>" required>
            
            <label for="security_answer2">Answer 2 (What is your first name?):</label>
            <input type="text" id="security_answer2" name="security_answer2" value="<?php echo htmlspecialchars($security2); ?>" required>
            
            <label for="security_answer3">Answer 3 (What is your last name?):</label>
            <input type="text" id="security_answer3" name="security_answer3" value="<?php echo htmlspecialchars($security3); ?>" required>
            
            <button type="submit">Update Profile</button>
        </form>
    </div>
</main>


<?php include_once("./includes/footer.php"); ?>

</body>
</html>
