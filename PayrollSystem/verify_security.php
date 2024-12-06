<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer1 = trim($_POST['answer1']);
    $answer2 = trim($_POST['answer2']);
    $answer3 = trim($_POST['answer3']);

    // Check if the user is in Forgot Password mode
    $forgot_password = isset($_SESSION['forgot_password']) && $_SESSION['forgot_password'] === true;

    // Connect to the database
    include_once("connection.php");

    // Fetch stored security answers for the logged-in user
    $stmt = $conn->prepare("SELECT security_answer1, security_answer2, security_answer3 FROM super_user WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($storedAnswer1, $storedAnswer2, $storedAnswer3);
        $stmt->fetch();

        // Initialize a counter for correct answers
        $correct_answers = 0;

        // Compare answers case-insensitively
        if (strcasecmp($answer1, $storedAnswer1) === 0) {
            $correct_answers++;
        }
        if (strcasecmp($answer2, $storedAnswer2) === 0) {
            $correct_answers++;
        }
        if (strcasecmp($answer3, $storedAnswer3) === 0) {
            $correct_answers++;
        }

        // Logic for Forgot Password vs Login
        if ($forgot_password) {
            // For Forgot Password, allow access if two or more answers are correct
            if ($correct_answers >= 2) {
                // Redirect to home
                unset($_SESSION['forgot_password']);
                header("Location: home.php");
                exit();
            } else {
                // Redirect back to login.php with an error
                header("Location: login.php?error=true");
                exit();
            }
        } else {
            // For Login, require all answers to be correct
            if ($correct_answers === 3) {
                // Redirect to home
                header("Location: home.php");
                exit();
            } else {
                // Redirect back to login.php with an error
                header("Location: login.php?error=true");
                exit();
            }
        }
    } else {
        // User not found
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
} else {
    // If the form wasn't submitted correctly
    header("Location: login.php");
    exit();
}