<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/config/database.php";

$db=new Database();
$conn=$db->connect();


$title=$_POST['title'];
$description=$_POST['description'];
$project=$_POST['project_id'];
$user=$_POST['assigned_to'];
$status=$_POST['status'];
$due=$_POST['due_date'];


$stmt=$conn->prepare("

INSERT INTO tasks
(title,description,project_id,assigned_to,status,due_date)

VALUES (?,?,?,?,?,?)

");

$stmt->bind_param(
"ssiiss",
$title,
$description,
$project,
$user,
$status,
$due
);

$stmt->execute();


header("Location:index.php?added=1");

exit;
