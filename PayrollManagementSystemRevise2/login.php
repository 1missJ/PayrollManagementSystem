<?php
session_start();

$error_message = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and sanitize input
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Connect to the database
    include_once("connection.php");

    // Prepare SQL query to fetch user by username
    $stmt = $conn->prepare("SELECT name, username, password FROM super_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($name, $storedUsername, $storedPassword);
        $stmt->fetch();

        // Check if the password matches
        if ($password === $storedPassword) {
            // Store username in session and redirect to the home page
            $_SESSION['Admin_User'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "Username not found. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wondersaw Enterprise Payroll System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            width: 100vw;
            margin: 0;
        }
        .background {
            background-image: url('wond.jpeg');
            background-position: center;
            background-size: cover;
            width: 70vw;
            height: 90vh;
        }
        .login-container {
            background-color: #fff;
            padding: 50px;
            margin-top: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: 31%;
            width: 310px;
            border-radius: 5px;
        }
        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
        }
        .login-container button {
            background-color: #FF7F50;
            color: #fff;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .login-container button:hover {
            background-color: #ff8c69;
        }
        .icon {
            padding: 10px;
            background-color: #ddd;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="background">
        <h1 style="margin-left: 8%; font-family: 'Times New Roman', Times, serif; font-size: 65px;">WONDERSAW ENTERPRISE</h1>
        <h2 style="margin-top: -35px; margin-left: 35%; font-family: 'Times New Roman', Times, serif; font-size: 38px;">PAYROLL SYSTEM</h2>
        <div class="login-container">
            <h1 style="margin-top: -15px;">LOG IN</h1>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>
            <form method="POST" action="">
                <div class="input-group">
                    <span class="icon">👤</span>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="input-group">
                    <span class="icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit">LOG IN</button><br><br>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </form>
        </div>
    </div>
</body>
</html>
