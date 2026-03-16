<?php

require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../helpers/api.php";
require_once __DIR__ . "/../helpers/database.php";
require_once __DIR__ . "/../helpers/logger.php";
require_once __DIR__ . "/../services/EmployeeService.php";

$conn = dbConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            requireRole('admin');
            apiResponse(true, "Employees fetched", employeeListAll($conn));

        case 'POST':
            requireRole('admin');
            $payload = apiReadJson();
            employeeCreate($conn, $payload);
            logActivity("Added employee: " . trim((string) ($payload['name'] ?? '')), currentUserId());
            apiResponse(true, "Employee added");

        case 'PUT':
            requireRole('admin');
            $id = apiRequireId();
            employeeUpdate($conn, $id, apiReadJson());
            logActivity("Updated employee ID " . $id, currentUserId());
            apiResponse(true, "Employee updated");

        case 'DELETE':
            requireRole('admin');
            $id = apiRequireId();
            employeeDelete($conn, $id);
            logActivity("Deleted employee ID " . $id, currentUserId());
            apiResponse(true, "Employee deleted");

        default:
            apiResponse(false, "Method not allowed", null, 405);
    }
} catch (Throwable $exception) {
    apiHandleException($exception);
}
