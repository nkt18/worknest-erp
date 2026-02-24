<?php
require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";
require_once dirname(__DIR__, 3) . "/helpers/logger.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$conn = $db->connect();

if (isset($_GET['id'])) {

    $id = intval($_GET['id']);


    $get = $conn->prepare("SELECT name FROM projects WHERE id = ?");
    $get->bind_param("i", $id);
    $get->execute();
    $result = $get->get_result();
    $project = $result->fetch_assoc();
    $get->close();

    if (!$project) {
        die("Project not found.");
    }

    $projectName = $project['name'];


    $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {

        logActivity(
            $_SESSION['user_id'],
            "Deleted project: " . $projectName
        );

        $stmt->close();

        header("Location: index.php?deleted=1");
        exit();
    } else {
        die("Project delete failed.");
    }
}