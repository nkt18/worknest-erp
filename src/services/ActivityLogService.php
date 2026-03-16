<?php

require_once __DIR__ . '/../helpers/database.php';
require_once __DIR__ . '/../helpers/entities.php';

function activityLogSearch($conn, $filters)
{
    $normalizedFilters = validateActivityLogFilters($filters);
    $search = $normalizedFilters['search'];
    $userId = $normalizedFilters['user_id'];
    $page = $normalizedFilters['page'];
    $limit = $normalizedFilters['limit'];
    $offset = ($page - 1) * $limit;

    $query = "
        SELECT activity_logs.*, users.name AS user_name
        FROM activity_logs
        LEFT JOIN users ON activity_logs.user_id = users.id
        WHERE 1 = 1
    ";

    $params = [];
    $types = '';

    if ($search !== '') {
        $query .= ' AND activity_logs.action LIKE ?';
        $params[] = '%' . $search . '%';
        $types .= 's';
    }

    if ($userId > 0) {
        $query .= ' AND activity_logs.user_id = ?';
        $params[] = $userId;
        $types .= 'i';
    }

    $countQuery = str_replace(
        'SELECT activity_logs.*, users.name AS user_name',
        'SELECT COUNT(*) AS total',
        $query
    );

    $countStmt = $conn->prepare($countQuery);

    if ($types !== '') {
        $countStmt->bind_param($types, ...$params);
    }

    dbExecuteOrFail($countStmt, 'Unable to load activity log count');
    $totalRows = (int) ($countStmt->get_result()->fetch_assoc()['total'] ?? 0);

    $query .= ' ORDER BY activity_logs.id DESC LIMIT ? OFFSET ?';
    $params[] = $limit;
    $params[] = $offset;
    $types .= 'ii';

    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    dbExecuteOrFail($stmt, 'Unable to load activity logs');

    return [
        'data' => array_map('serializeActivityLog', dbFetchAll($stmt->get_result())),
        'totalPages' => max(1, (int) ceil($totalRows / $limit)),
    ];
}
