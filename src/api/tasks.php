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

SELECT tasks.*,
projects.name AS project_name,
users.name AS user_name

FROM tasks

LEFT JOIN projects
ON tasks.project_id=projects.id

LEFT JOIN users
ON tasks.assigned_to=users.id

ORDER BY tasks.id ASC

");

$data=[];

while($row=$result->fetch_assoc()){

$data[]=$row;

}

respond(true,"Tasks fetched",$data);

break;

case "POST":

$data=json_decode(file_get_contents("php://input"),true);

$title=$data['title'];
$description=$data['description'];
$project=$data['project_id'];
$user=$data['assigned_to'];
$status=$data['status'];
$due=$data['due_date'];


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

respond(true,"Task added");

break;


case "PUT":

parse_str($_SERVER['QUERY_STRING'],$params);

$id=$params['id'];

$data=json_decode(file_get_contents("php://input"),true);

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
$data['title'],
$data['description'],
$data['project_id'],
$data['assigned_to'],
$data['status'],
$data['due_date'],
$id
);

$stmt->execute();

respond(true,"Task updated");

break;

case "DELETE":

parse_str($_SERVER['QUERY_STRING'],$params);

$id=$params['id'];

$stmt=$conn->prepare("

DELETE FROM tasks
WHERE id=?

");

$stmt->bind_param("i",$id);

$stmt->execute();

respond(true,"Task deleted");

break;

}
