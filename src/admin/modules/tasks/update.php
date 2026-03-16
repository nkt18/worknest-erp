<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";
require_once dirname(__DIR__,3)."/helpers/database.php";
require_once dirname(__DIR__,3)."/helpers/logger.php";
require_once dirname(__DIR__,3)."/services/TaskService.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    taskUpdateByAdmin(dbConnection(), (int) ($_POST['task_id'] ?? 0), $_POST);
    logActivity("Updated task ID " . (int) ($_POST['task_id'] ?? 0), currentUserId());
    header("Location:index.php?updated=1");
    exit;
} catch (Throwable $exception) {
    header("Location:index.php?error=" . urlencode($exception->getMessage()));
    exit;
}
