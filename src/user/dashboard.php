<?php

require_once __DIR__ . "/../middleware/user.php";

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";

?>

<div class="content">

<div class="mb-4">

<h4 class="fw-semibold">
Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>
</h4>

<small class="text-muted">
Here is your task overview
</small>

</div>



<div class="row g-4">


<div class="col-md-3">
<div class="card shadow-sm border-0 p-3">

<small class="text-muted">
Total Tasks
</small>

<h3 id="totalTasks"
class="fw-bold mt-2">

Loading...

</h3>

</div>
</div>



<div class="col-md-3">
<div class="card shadow-sm border-0 p-3">

<small class="text-muted">
Completed
</small>

<h3 id="completed"
class="fw-bold text-success mt-2">

Loading...

</h3>

</div>
</div>



<div class="col-md-3">
<div class="card shadow-sm border-0 p-3">

<small class="text-muted">
In Progress
</small>

<h3 id="inProgress"
class="fw-bold text-info mt-2">

Loading...

</h3>

</div>
</div>



<div class="col-md-3">
<div class="card shadow-sm border-0 p-3">

<small class="text-muted">
Pending
</small>

<h3 id="pending"
class="fw-bold text-warning mt-2">

Loading...

</h3>

</div>
</div>


</div>

</div>



<script>

fetch("/worknest-erp/src/api/tasks.php")

.then(response => response.json())

.then(data => {


if(!data.success){

showError();

return;

}


let tasks=data.data || [];


let total=tasks.length;
let completed=0;
let inProgress=0;
let pending=0;


tasks.forEach(t=>{

if(t.status==="completed")
completed++;

if(t.status==="in_progress")
inProgress++;

if(t.status==="pending")
pending++;

});


document.getElementById("totalTasks").innerText=total;
document.getElementById("completed").innerText=completed;
document.getElementById("inProgress").innerText=inProgress;
document.getElementById("pending").innerText=pending;


})


.catch(error => {

showError();

});

function showError(){

document.getElementById("totalTasks").innerText="0";
document.getElementById("completed").innerText="0";
document.getElementById("inProgress").innerText="0";
document.getElementById("pending").innerText="0";

}

</script>



<?php require_once __DIR__ . "/../layout/footer.php"; ?>