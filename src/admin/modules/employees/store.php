<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

if($_SERVER['REQUEST_METHOD']=="POST"){

$data=[
"name"=>$_POST['name'],
"email"=>$_POST['email'],
"designation"=>$_POST['designation'],
"phone"=>$_POST['phone'],
"department"=>$_POST['department']
];

$options=[
'http'=>[
'method'=>'POST',
'header'=>"Content-Type: application/json",
'content'=>json_encode($data)
]
];

$context=stream_context_create($options);

file_get_contents(
"http://localhost/worknest-erp/src/api/employees.php",
false,
$context
);

header("Location:index.php");
exit();

}
