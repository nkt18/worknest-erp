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

if (isset($_SESSION['LAST_ACTIVITY']) &&
    (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {

    session_unset();
    session_destroy();
    header("Location: /worknest-erp/src/auth/login.php?timeout=1");
    exit();
}

$_SESSION['LAST_ACTIVITY'] = time();