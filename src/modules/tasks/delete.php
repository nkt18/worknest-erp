<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->connect();

if(isset($_GET['id'])){
    $id = intval($_GET['id']);

    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: index.php?deleted=1");
    exit();
}