<?php
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $answer1 = trim($_POST['answer1']);
    $answer2 = trim($_POST['answer2']);
    $answer3 = trim($_POST['answer3']);

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

        // Compare answers
        if ($answer1 === $storedAnswer1 && $answer2 === $storedAnswer2 && $answer3 === $storedAnswer3) {
            // Correct answers, redirect to home
            header("Location: home.php");
            exit();
        } else {
            // Incorrect answers, redirect back to login.php with error message
            header("Location: login.php?error=true");
            exit();
        }
    } else {
        // User not found
        echo "User not found.";
    }

    $stmt->close();
    $conn->close();
}
