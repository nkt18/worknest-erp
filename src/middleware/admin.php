<?php
require_once __DIR__ . "/auth.php";

if ($_SESSION['user_role'] !== 'admin') {
    echo "<h3 style='color:red;'>Access Denied. Admin Only.</h3>";
    exit();
}
?>