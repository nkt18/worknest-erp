<?php
require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";

$db = new Database();
$conn = $db->connect();

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $id = intval($_POST['task_id']);
    $title = trim($_POST['title']);
    $project_id = intval($_POST['project_id']);
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : NULL;
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE tasks 
                            SET title=?, project_id=?, assigned_to=?, status=?, due_date=? 
                            WHERE id=?");

    $stmt->bind_param("siissi", $title, $project_id, $assigned_to, $status, $due_date, $id);

    $stmt->execute();

    header("Location: index.php?updated=1");
    exit();
}