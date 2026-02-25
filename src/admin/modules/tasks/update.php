<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/config/database.php";

$db=new Database();
$conn=$db->connect();


$id=$_POST['task_id'];

$title=$_POST['title'];
$description=$_POST['description'];
$project=$_POST['project_id'];
$user=$_POST['assigned_to'];
$status=$_POST['status'];
$due=$_POST['due_date'];


$stmt=$conn->prepare("

UPDATE tasks

SET title=?,
description=?,
project_id=?,
assigned_to=?,
status=?,
due_date=?

WHERE id=?

");

$stmt->bind_param(
"ssiissi",
$title,
$description,
$project,
$user,
$status,
$due,
$id
);

$stmt->execute();


header("Location:index.php?updated=1");

exit;
