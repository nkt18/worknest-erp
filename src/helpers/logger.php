<?php
require_once __DIR__ . "/../config/database.php";

function logActivity($action)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        return; // no logging if not logged in
    }

    $db = new Database();
    $conn = $db->connect();

    $stmt = $conn->prepare("INSERT INTO activity_logs (user_id, action) VALUES (?, ?)");
    $stmt->bind_param("is", $_SESSION['user_id'], $action);
    $stmt->execute();
}