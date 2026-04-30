<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize the multidimensional array if it doesn't exist 
if (!isset($_SESSION['employees'])) {
    $_SESSION['employees'] = [];
}

// Handle the "Reset All Data" button [cite: 22]
if (isset($_POST['reset_data'])) {
    $_SESSION['employees'] = [];
    header("Location: index.php");
    exit();
}
?>