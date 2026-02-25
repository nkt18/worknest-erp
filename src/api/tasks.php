<?php

require_once __DIR__."/../middleware/auth.php";
require_once __DIR__."/../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];

function respond($success,$message="",$data=[]){

echo json_encode([
"success"=>$success,
"message"=>$message,
"data"=>$data
]);

exit();

}


if($method=="GET"){


if(session_status()===PHP_SESSION_NONE){
session_start();
}


if(isset($_SESSION['user_role'])
&& $_SESSION['user_role']=="user"){

$userId=(int)$_SESSION['user_id'];

$sql="

SELECT tasks.*,
projects.name AS project_name,
users.name AS user_name

FROM tasks

LEFT JOIN projects
ON tasks.project_id=projects.id

LEFT JOIN users
ON tasks.assigned_to=users.id

WHERE tasks.assigned_to=$userId

ORDER BY tasks.id DESC

";

}

else{

$sql="

SELECT tasks.*,
projects.name AS project_name,
users.name AS user_name

FROM tasks

LEFT JOIN projects
ON tasks.project_id=projects.id

LEFT JOIN users
ON tasks.assigned_to=users.id

ORDER BY tasks.id DESC

";

}


$result=$conn->query($sql);

$data=[];

while($row=$result->fetch_assoc()){

$data[]=$row;

}

respond(true,"Tasks Loaded",$data);

}

if($method=="POST"){

$data=json_decode(file_get_contents("php://input"),true);


$title=trim($data['title'] ?? "");
$description=trim($data['description'] ?? "");
$project=(int)($data['project_id'] ?? 0);
$user=(int)($data['assigned_to'] ?? 0);
$status=$data['status'] ?? "pending";
$due=$data['due_date'] ?? null;


/* VALIDATION */

if($title==""){
respond(false,"Title required");
}

if($description==""){
respond(false,"Description required");
}


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

respond(true,"Task Added");

}


if($method=="PUT"){

parse_str($_SERVER['QUERY_STRING'],$params);

$id=(int)($params['id'] ?? 0);

$data=json_decode(file_get_contents("php://input"),true);


/* USER STATUS UPDATE ONLY */

if(isset($_SESSION['user_role'])
&& $_SESSION['user_role']=="user"){

$status=$data['status'] ?? "pending";

$stmt=$conn->prepare("

UPDATE tasks

SET status=?

WHERE id=?
AND assigned_to=?

");

$stmt->bind_param(
"sii",
$status,
$id,
$_SESSION['user_id']
);

$stmt->execute();

respond(true,"Status Updated");

}

$title=trim($data['title'] ?? "");
$description=trim($data['description'] ?? "");
$project=(int)($data['project_id'] ?? 0);
$user=(int)($data['assigned_to'] ?? 0);
$status=$data['status'] ?? "pending";
$due=$data['due_date'] ?? null;


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

respond(true,"Task Updated");

}

if($method=="DELETE"){

parse_str($_SERVER['QUERY_STRING'],$params);

$id=(int)($params['id'] ?? 0);


$stmt=$conn->prepare("

DELETE FROM tasks
WHERE id=?

");

$stmt->bind_param("i",$id);

$stmt->execute();

respond(true,"Task Deleted");

}

respond(false,"Invalid Request");