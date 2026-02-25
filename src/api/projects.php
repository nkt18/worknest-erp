<?php

require_once __DIR__."/../middleware/auth.php";
require_once __DIR__."/../config/database.php";

header("Content-Type: application/json");

$db=new Database();
$conn=$db->connect();

$method=$_SERVER['REQUEST_METHOD'];

function respond($success,$message="",$data=null){

echo json_encode([
"success"=>$success,
"message"=>$message,
"data"=>$data
]);

exit;

}

switch($method){


case "GET":

$result=$conn->query("
SELECT *
FROM projects
ORDER BY id ASC
");

$projects=[];

while($row=$result->fetch_assoc()){

$projects[]=$row;

}

respond(true,"Projects fetched",$projects);

break;


case "POST":

$data=json_decode(file_get_contents("php://input"),true);

$name=$data['name'];
$description=$data['description'];
$status=$data['status'];
$start=$data['start_date'];
$end=$data['end_date'];

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

respond(true,"Project added");

break;

case "PUT":

parse_str($_SERVER['QUERY_STRING'],$params);

$id=$params['id'];

$data=json_decode(file_get_contents("php://input"),true);

$stmt=$conn->prepare("
UPDATE projects
SET name=?,description=?,status=?,start_date=?,end_date=?
WHERE id=?
");

$stmt->bind_param(
"sssssi",
$data['name'],
$data['description'],
$data['status'],
$data['start_date'],
$data['end_date'],
$id
);

$stmt->execute();

respond(true,"Project updated");

break;


case "DELETE":

parse_str($_SERVER['QUERY_STRING'],$params);

$id=$params['id'];

$stmt=$conn->prepare("
DELETE FROM projects
WHERE id=?
");

$stmt->bind_param("i",$id);

$stmt->execute();

respond(true,"Project deleted");

break;

}