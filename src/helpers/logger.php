<?php
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/session.php';

function logActivity($action, $userId = null)
{
    startSecureSession();

    $actorId = $userId ?? ($_SESSION['user_id'] ?? null);

    if (!$actorId) {
        return;
    }

    $conn = dbConnection();

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $actorId, $action);
    $stmt->execute();
}
