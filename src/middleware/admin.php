<?php
require_once __DIR__ . "/auth.php";

if ($_SESSION['user_role'] !== 'admin') {
    echo "NOT ADMIN";
    exit();
}