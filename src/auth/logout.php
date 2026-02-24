<?php
// Start session
session_start();
session_unset();
session_destroy();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Redirect to login page
header("Location: /worknest-erp/src/auth/login.php");
exit();
?>