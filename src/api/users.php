<?php

require_once __DIR__."/../middleware/auth.php";
require_once __DIR__."/../config/database.php";

header("Content-Type: application/json");

$db=new Database();
$conn=$db->connect();

$result=$conn->query("
SELECT id,name
FROM users
ORDER BY name
");

$data=[];

while($row=$result->fetch_assoc()){
$data[]=$row;
}

echo json_encode([
"success"=>true,
"data"=>$data
]);