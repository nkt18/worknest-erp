<?php

require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../helpers/api.php";
require_once __DIR__ . "/../helpers/database.php";
require_once __DIR__ . "/../helpers/logger.php";
require_once __DIR__ . "/../services/TaskService.php";

$conn = dbConnection();
$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === "GET") {
        if (currentUserRole() === "user") {
            apiResponse(true, "Tasks loaded", taskListForUser($conn, currentUserId()));
        }

        requireRole('admin');
        apiResponse(true, "Tasks loaded", taskListForAdmin($conn));
    }

    if ($method === "POST") {
        requireRole('admin');
        $payload = apiReadJson();
        taskCreate($conn, $payload);
        logActivity("Added task: " . trim((string) ($payload['title'] ?? '')), currentUserId());
        apiResponse(true, "Task added");
    }

    if ($method === "PUT") {
        $id = apiRequireId();
        $payload = apiReadJson();

        if (currentUserRole() === "user") {
            taskUpdateStatusForUser($conn, $id, currentUserId(), $payload['status'] ?? 'pending');
            logActivity("Updated task status for task ID " . $id, currentUserId());
            apiResponse(true, "Status updated");
        }

        requireRole('admin');
        taskUpdateByAdmin($conn, $id, $payload);
        logActivity("Updated task ID " . $id, currentUserId());
        apiResponse(true, "Task updated");
    }

    if ($method === "DELETE") {
        requireRole('admin');
        $id = apiRequireId();
        taskDelete($conn, $id);
        logActivity("Deleted task ID " . $id, currentUserId());
        apiResponse(true, "Task deleted");
    }

    apiResponse(false, "Invalid request", null, 405);
} catch (Throwable $exception) {
    apiHandleException($exception);
}
