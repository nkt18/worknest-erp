<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";
session_start();

$db = new Database();
$conn = $db->connect();

if($_SERVER["REQUEST_METHOD"]==="POST"){
$name=$_POST['name'];
$description=$_POST['description'];
$status=$_POST['status'];
$start=$_POST['start_date'];
$end=$_POST['end_date'];
$created_by=$_SESSION['user_id'];

$stmt=$conn->prepare("INSERT INTO projects (name,description,status,start_date,end_date,created_by) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("sssssi",$name,$description,$status,$start,$end,$created_by);
$stmt->execute();

header("Location: index.php?added=1");
exit();
}