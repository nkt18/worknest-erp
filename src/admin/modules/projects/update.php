<?php
require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";
require_once dirname(__DIR__, 3) . "/helpers/logger.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $id = intval($_POST['project_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];

    $stmt = $conn->prepare(
        "UPDATE projects 
         SET name=?, description=?, status=?, start_date=?, end_date=? 
         WHERE id=?"
    );

    $stmt->bind_param("sssssi", $name, $description, $status, $start, $end, $id);

    if ($stmt->execute()) {

        logActivity(
            $_SESSION['user_id'],
            "Updated project: " . $name
        );

        $stmt->close();

        header("Location: index.php?updated=1");
        exit();
    } else {
        die("Project update failed.");
    }
}