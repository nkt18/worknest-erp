<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/config/database.php";

$db=new Database();
$conn=$db->connect();


$id=$_GET['id'];

$stmt=$conn->prepare("
DELETE FROM projects
WHERE id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();


header("Location:index.php?deleted=1");

exit;