<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/config/database.php";

$db=new Database();
$conn=$db->connect();


$id=$_POST['project_id'];

$name=$_POST['name'];
$description=$_POST['description'];
$status=$_POST['status'];
$start=$_POST['start_date'];
$end=$_POST['end_date'];


$stmt=$conn->prepare("
UPDATE projects
SET name=?,description=?,status=?,start_date=?,end_date=?
WHERE id=?
");

$stmt->bind_param(
"sssssi",
$name,
$description,
$status,
$start,
$end,
$id
);

$stmt->execute();


header("Location:index.php?updated=1");

exit;
