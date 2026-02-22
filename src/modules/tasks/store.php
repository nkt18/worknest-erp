<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->connect();

if($_SERVER["REQUEST_METHOD"] === "POST"){

    $title = trim($_POST['title']);
    $project_id = intval($_POST['project_id']);
    $assigned_to = !empty($_POST['assigned_to']) ? intval($_POST['assigned_to']) : NULL;
    $status = $_POST['status'];
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO tasks (title, project_id, assigned_to, status, due_date)
                            VALUES (?, ?, ?, ?, ?)");

    $stmt->bind_param("siiss", $title, $project_id, $assigned_to, $status, $due_date);

    if($stmt->execute()){
        header("Location: index.php?added=1");
        exit();
    } else {
        die("Task insert failed.");
    }
}