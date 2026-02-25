<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

$id=$_GET['id'];

$options=[
'http'=>[
'method'=>'DELETE'
]
];

$context=stream_context_create($options);

file_get_contents(
"http://localhost/worknest-erp/src/api/employees.php?id=".$id,
false,
$context
);

header("Location:index.php");
exit();
