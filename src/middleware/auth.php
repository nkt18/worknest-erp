<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /worknest-erp/src/auth/login.php");
    exit();
}
?>