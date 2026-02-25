<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/config/database.php";

$db=new Database();
$conn=$db->connect();


$name=$_POST['name'];
$description=$_POST['description'];
$status=$_POST['status'];
$start=$_POST['start_date'];
$end=$_POST['end_date'];

$user=$_SESSION['user_id'];


$stmt=$conn->prepare("
INSERT INTO projects
(name,description,status,start_date,end_date,created_by)
VALUES (?,?,?,?,?,?)
");

$stmt->bind_param(
"sssssi",
$name,
$description,
$status,
$start,
$end,
$user
);

$stmt->execute();


header("Location:index.php?added=1");

exit;
