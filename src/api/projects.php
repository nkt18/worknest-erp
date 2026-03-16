<?php

require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../helpers/api.php";
require_once __DIR__ . "/../helpers/database.php";
require_once __DIR__ . "/../helpers/logger.php";
require_once __DIR__ . "/../services/ProjectService.php";

$conn = dbConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case "GET":
            apiResponse(true, "Projects fetched", projectListAll($conn));

        case "POST":
            requireRole('admin');
            $payload = apiReadJson();
            projectCreate($conn, $payload, currentUserId());
            logActivity("Added project: " . trim((string) ($payload['name'] ?? '')), currentUserId());
            apiResponse(true, "Project added");

        case "PUT":
            requireRole('admin');
            $id = apiRequireId();
            $payload = apiReadJson();
            projectUpdate($conn, $id, $payload);
            logActivity("Updated project ID " . $id, currentUserId());
            apiResponse(true, "Project updated");

        case "DELETE":
            requireRole('admin');
            $id = apiRequireId();
            projectDelete($conn, $id);
            logActivity("Deleted project ID " . $id, currentUserId());
            apiResponse(true, "Project deleted");

        default:
            apiResponse(false, "Method not allowed", null, 405);
    }
} catch (Throwable $exception) {
    apiHandleException($exception);
}
