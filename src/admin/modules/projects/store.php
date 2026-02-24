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

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $status = $_POST['status'];
    $start = $_POST['start_date'];
    $end = $_POST['end_date'];
    $created_by = $_SESSION['user_id'];

    $stmt = $conn->prepare(
        "INSERT INTO projects 
        (name, description, status, start_date, end_date, created_by) 
        VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("sssssi", $name, $description, $status, $start, $end, $created_by);

    if ($stmt->execute()) {

        logActivity(
            $_SESSION['user_id'],
            "Created new project: " . $name
        );

        $stmt->close();

        header("Location: index.php?added=1");
        exit();
    } else {
        die("Project insert failed.");
    }
}