<?php

require_once __DIR__ . "/../middleware/admin.php";
require_once __DIR__ . "/../helpers/api.php";
require_once __DIR__ . "/../helpers/database.php";
require_once __DIR__ . "/../services/ActivityLogService.php";

try {
    $result = activityLogSearch(dbConnection(), [
        'search' => $_GET['search'] ?? '',
        'user_id' => $_GET['user_id'] ?? '',
        'page' => $_GET['page'] ?? 1,
        'limit' => 5,
    ]);

    apiResponse(true, "Activity logs fetched", $result);
} catch (Throwable $exception) {
    apiHandleException($exception);
}
