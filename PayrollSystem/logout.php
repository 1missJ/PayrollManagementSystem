<?php
if (isset($_GET['logout'])) {
    // Destroy session and logout
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}