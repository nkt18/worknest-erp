<?php

require_once __DIR__ . "/../middleware/auth.php";
require_once __DIR__ . "/../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$method = $_SERVER['REQUEST_METHOD'];
$userId = $_SESSION['user_id'];
$userRole = $_SESSION['user_role'];

function respond($success,$message="",$data=null,$code=200){
    http_response_code($code);
    echo json_encode([
        "success"=>$success,
        "message"=>$message,
        "data"=>$data
    ]);
    exit;
}

switch($method){

/* ================= GET ================= */

case 'GET':

$result=$conn->query("
SELECT 
employees.id,
users.name,
users.email,
employees.designation,
employees.phone,
employees.department
FROM employees
JOIN users ON employees.user_id=users.id
ORDER BY employees.id ASC
");

$data=[];

while($row=$result->fetch_assoc()){
$data[]=$row;
}

respond(true,"Employees fetched",$data);

break;


/* ================= CREATE ================= */

case 'POST':

if($userRole!='admin'){
respond(false,"Unauthorized",null,403);
}

$data=json_decode(file_get_contents("php://input"),true);

$name=$data['name']??'';
$email=$data['email']??'';
$designation=$data['designation']??'';
$phone=$data['phone']??'';
$department=$data['department']??'';

if(!$name||!$email||!$designation||!$phone||!$department){
respond(false,"All fields required");
}

/* create user */

$password=password_hash("123456",PASSWORD_DEFAULT);

$stmt=$conn->prepare("
INSERT INTO users(name,email,password,role)
VALUES(?,?,?,'user')
");

$stmt->bind_param("sss",$name,$email,$password);
$stmt->execute();

$newUserId=$stmt->insert_id;

/* create employee */

$stmt2=$conn->prepare("
INSERT INTO employees(user_id,designation,phone,department)
VALUES(?,?,?,?)
");

$stmt2->bind_param("isss",$newUserId,$designation,$phone,$department);
$stmt2->execute();

/* activity log */

$action="Added employee: ".$name;

$log=$conn->prepare("
INSERT INTO activity_logs(user_id,action)
VALUES(?,?)
");

$log->bind_param("is",$userId,$action);
$log->execute();

respond(true,"Employee Added");

break;


/* ================= UPDATE ================= */

case 'PUT':

if($userRole!='admin'){
respond(false,"Unauthorized",null,403);
}

parse_str($_SERVER['QUERY_STRING'],$params);
$id=$params['id']??null;

$data=json_decode(file_get_contents("php://input"),true);

$name=$data['name']??'';
$email=$data['email']??'';
$designation=$data['designation']??'';
$phone=$data['phone']??'';
$department=$data['department']??'';

$stmt=$conn->prepare("
UPDATE employees
JOIN users ON employees.user_id=users.id
SET
users.name=?,
users.email=?,
employees.designation=?,
employees.phone=?,
employees.department=?
WHERE employees.id=?
");

$stmt->bind_param(
"sssssi",
$name,
$email,
$designation,
$phone,
$department,
$id
);

$stmt->execute();

$action="Updated employee ID ".$id;

$log=$conn->prepare("
INSERT INTO activity_logs(user_id,action)
VALUES(?,?)
");

$log->bind_param("is",$userId,$action);
$log->execute();

respond(true,"Employee Updated");

break;


/* ================= DELETE ================= */

case 'DELETE':

if($userRole!='admin'){
respond(false,"Unauthorized",null,403);
}

parse_str($_SERVER['QUERY_STRING'],$params);
$id=$params['id']??null;

$stmt=$conn->prepare("
SELECT user_id FROM employees WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

$userIdDelete=$stmt->get_result()->fetch_assoc()['user_id'];

/* delete employee */

$stmt=$conn->prepare("
DELETE FROM employees WHERE id=?
");

$stmt->bind_param("i",$id);
$stmt->execute();

/* delete user */

$stmt=$conn->prepare("
DELETE FROM users WHERE id=?
");

$stmt->bind_param("i",$userIdDelete);
$stmt->execute();

/* activity log */

$action="Deleted employee ID ".$id;

$log=$conn->prepare("
INSERT INTO activity_logs(user_id,action)
VALUES(?,?)
");

$log->bind_param("is",$userId,$action);
$log->execute();

respond(true,"Employee Deleted");

break;


default:

respond(false,"Method not allowed",null,405);

}
