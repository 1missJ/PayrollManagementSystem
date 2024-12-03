<?php
session_start();

$error_message = '';
$show_modal = false;

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    include_once("connection.php");

    $stmt = $conn->prepare("SELECT id, name, username, password FROM super_user WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $storedUsername, $storedPassword);
        $stmt->fetch();

        if ($password === $storedPassword) {
            $_SESSION['user_id'] = $id;
            $_SESSION['Admin_User'] = $username;
            $show_modal = true;
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "Username not found. Please try again.";
    }

    $stmt->close();
    $conn->close();
}

if (isset($_GET['error'])) {
    $error_message = "Incorrect answers. Please try again.";
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
            background-image: url('./assets/images/wond.jpeg');
            background-position: center;
            background-size: cover;
            width: 70vw;
            height: 90vh;
        }
        .login-container {
            background-color: #fff;
            padding: 60px;
            margin-top: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            margin-left: 31%;
            width: 400px;
            border-radius: 5px;
        }
        .login-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }
        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 5px;
        }
        .input-group .icon {
            font-size: 18px;
            margin-right: 10px;
            color: #888;
        }
        .input-group input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 16px;
            padding: 10px;
        }
        .input-group input::placeholder {
            font-size: 14px;
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
        .error {
            color: red;
            font-size: 14px;
        }
    </style>
    <!-- Include Bootstrap for modal functionality -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="background">
        <h1 style="margin-left: 8%; font-family: 'Times New Roman', Times, serif; font-size: 65px;">WONDERSAW ENTERPRISE</h1>
        <br>
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
                <button type="submit">LOGIN</button><br><br>
                <a href="#" class="forgot-password">Forgot Password?</a>
            </form>
        </div>
    </div>

    <!-- Security Questions Modal -->
    <?php if ($show_modal): ?>
    <div class="modal fade show" id="securityModal" tabindex="-1" role="dialog" aria-labelledby="securityModalLabel" aria-hidden="true" style="display: block;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="securityModalLabel">Security Questions</h5>
                </div>
                <div class="modal-body">
                    <form id="securityForm" method="POST" action="verify_security.php">
                        <div class="form-group">
                            <label for="answer1">What is your hobby?</label>
                            <input type="text" class="form-control" id="answer1" name="answer1" required>
                        </div>
                        <div class="form-group">
                            <label for="answer2">What is your first name?</label>
                            <input type="text" class="form-control" id="answer2" name="answer2" required>
                        </div>
                        <div class="form-group">
                            <label for="answer3">What is your last name?</label>
                            <input type="text" class="form-control" id="answer3" name="answer3" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('#securityModal').modal({ backdrop: 'static', keyboard: false });
        });
    </script>
    <?php endif; ?>
</body>
</html>
