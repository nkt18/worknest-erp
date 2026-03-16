<?php

require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../helpers/api.php";
require_once __DIR__ . "/../helpers/database.php";
require_once __DIR__ . "/../helpers/entities.php";

requireRole('admin');

$conn = dbConnection();

$result = $conn->query("
    SELECT id, name
    FROM users
    ORDER BY name
");

$data = array_map('serializeUserSummary', dbFetchAll($result));

apiResponse(true, "Users fetched", $data);
