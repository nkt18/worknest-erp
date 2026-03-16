<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";
require_once dirname(__DIR__,3)."/helpers/database.php";
require_once dirname(__DIR__,3)."/helpers/logger.php";
require_once dirname(__DIR__,3)."/services/EmployeeService.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    employeeCreate(dbConnection(), $_POST);
    logActivity("Added employee: " . trim((string) ($_POST['name'] ?? '')), currentUserId());
    header("Location:index.php?added=1");
    exit;
} catch (Throwable $exception) {
    header("Location:index.php?error=" . urlencode($exception->getMessage()));
    exit;
}
