<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";
require_once dirname(__DIR__,3)."/helpers/database.php";
require_once dirname(__DIR__,3)."/helpers/logger.php";
require_once dirname(__DIR__,3)."/services/ProjectService.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

try {
    projectCreate(dbConnection(), $_POST, currentUserId());
    logActivity("Added project: " . trim((string) ($_POST['name'] ?? '')), currentUserId());
    header("Location:index.php?added=1");
    exit;
} catch (Throwable $exception) {
    header("Location:index.php?error=" . urlencode($exception->getMessage()));
    exit;
}
