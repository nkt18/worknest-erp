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
    $id = (int) ($_POST['id'] ?? 0);
    employeeDelete(dbConnection(), $id);
    logActivity("Deleted employee ID " . $id, currentUserId());
    header("Location:index.php?deleted=1");
    exit;
} catch (Throwable $exception) {
    header("Location:index.php?error=" . urlencode($exception->getMessage()));
    exit;
}
