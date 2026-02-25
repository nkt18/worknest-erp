<?php
require_once __DIR__ . "/auth.php";

if ($_SESSION['user_role'] !== 'user') {
    echo "NOT USER";
    exit();
}