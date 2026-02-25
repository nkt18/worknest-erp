<?php

require_once "../middleware/admin.php";
require_once "../config/database.php";

header("Content-Type: application/json");

$db = new Database();
$conn = $db->connect();

$search = $_GET['search'] ?? '';
$user_id = $_GET['user_id'] ?? '';

/* PAGINATION */

$limit = 5;
$page = $_GET['page'] ?? 1;

if($page<1) $page=1;

$offset = ($page-1)*$limit;

/* BASE QUERY */

$query="SELECT activity_logs.*, users.name AS user_name
FROM activity_logs
LEFT JOIN users
ON activity_logs.user_id = users.id
WHERE 1=1";

$params=[];
$types="";


if(!empty($search)){

$query.=" AND activity_logs.action LIKE ?";

$params[]="%$search%";

$types.="s";

}


if(!empty($user_id)){

$query.=" AND activity_logs.user_id=?";

$params[]=$user_id;

$types.="i";

}

/* COUNT */

$countQuery=str_replace(
"SELECT activity_logs.*, users.name AS user_name",
"SELECT COUNT(*) as total",
$query
);

$countStmt=$conn->prepare($countQuery);

if(!empty($params)){

$countStmt->bind_param($types,...$params);

}

$countStmt->execute();

$totalRows=$countStmt
->get_result()
->fetch_assoc()['total'];

$totalPages=ceil($totalRows/$limit);

/* FETCH DATA */

$query.=" ORDER BY activity_logs.id ASC LIMIT ? OFFSET ?";

$params[]=$limit;
$params[]=$offset;

$types.="ii";

$stmt=$conn->prepare($query);

$stmt->bind_param($types,...$params);

$stmt->execute();

$result=$stmt->get_result();

$data=[];

while($row=$result->fetch_assoc()){

$data[]=$row;

}

/* RESPONSE */

echo json_encode([

"success"=>true,
"data"=>$data,
"totalPages"=>$totalPages

]);