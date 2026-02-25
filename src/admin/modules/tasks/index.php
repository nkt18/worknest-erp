<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/layout/admin_header.php";
require_once dirname(__DIR__,3)."/layout/sidebar_admin.php";
require_once dirname(__DIR__,3)."/layout/navbar_admin.php";

?>

<div class="content">

<h4 class="mb-4 fw-semibold">Task Management</h4>

<div class="card dashboard-card p-4">


<div class="row mb-3">

<div class="col-md-4">

<input type="text"
id="searchBox"
class="form-control"
placeholder="Search task">

</div>

<div class="col-md-3">

<select id="statusFilter" class="form-select">

<option value="">All Status</option>
<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>

</select>

</div>

<div class="col-md-3">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addModal">

+ Add Task

</button>

</div>

</div>


<table class="table table-hover">

<thead class="table-light">

<tr>

<th>ID</th>
<th>Title</th>
<th>Project</th>
<th>User</th>
<th>Status</th>

<th width="150">Actions</th>

</tr>

</thead>


<tbody id="taskTable">

<tr>

<td colspan="6"
class="text-center text-muted">

Loading tasks...

</td>

</tr>

</tbody>

</table>



<nav>

<ul class="pagination justify-content-center"
id="pagination">

</ul>

</nav>


</div>

</div>


<!-- ADD MODAL -->

<div class="modal fade" id="addModal">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5>Add Task</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input id="addTitle"
class="form-control mb-2"
placeholder="Title">

<textarea id="addDescription"
class="form-control mb-2"
placeholder="Description"></textarea>


<select id="addProject"
class="form-control mb-2"></select>


<select id="addUser"
class="form-control mb-2"></select>


<select id="addStatus"
class="form-control mb-2">

<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>

</select>


<input type="date"
id="addDue"
class="form-control mb-2">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="addTask()">

Save

</button>

</div>

</div>

</div>

</div>



<!-- EDIT MODAL -->

<div class="modal fade" id="editModal">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5>Edit Task</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input type="hidden" id="editId">

<input id="editTitle"
class="form-control mb-2">


<textarea id="editDescription"
class="form-control mb-2"></textarea>


<select id="editProject"
class="form-control mb-2"></select>


<select id="editUser"
class="form-control mb-2"></select>


<select id="editStatus"
class="form-control mb-2">

<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>

</select>


<input type="date"
id="editDue"
class="form-control mb-2">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="updateTask()">

Update

</button>

</div>

</div>

</div>

</div>



<!-- DELETE MODAL -->

<div class="modal fade" id="deleteModal">

<div class="modal-dialog">

<div class="modal-content">

<div class="modal-header">

<h5>Confirm Delete</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

Delete this task?

</div>

<div class="modal-footer">

<button class="btn btn-danger"
onclick="deleteTask()">

Delete

</button>

</div>

</div>

</div>

</div>



<script>

let tasks=[];
let filtered=[];
let page=1;
let limit=5;
let deleteId=null;


/* LOAD */

loadTasks();

loadProjects();

loadUsers();



function loadTasks(){

fetch("/worknest-erp/src/api/tasks.php")

.then(res=>res.json())

.then(data=>{

tasks=data.data;

applyFilters();

});

}



/* LOAD PROJECTS */

function loadProjects(){

fetch("/worknest-erp/src/api/projects.php")

.then(res=>res.json())

.then(data=>{

let options="";

data.data.forEach(p=>{

options+=`<option value="${p.id}">${p.name}</option>`;

});

addProject.innerHTML=options;
editProject.innerHTML=options;

});

}



/* LOAD USERS */

function loadUsers(){

fetch("/worknest-erp/src/api/employees.php")

.then(res=>res.json())

.then(data=>{

let options="";

data.data.forEach(u=>{

options+=`<option value="${u.id}">${u.name}</option>`;

});

addUser.innerHTML=options;
editUser.innerHTML=options;

});

}



/* FILTER */

searchBox.addEventListener("keyup",applyFilters);
statusFilter.addEventListener("change",applyFilters);



function applyFilters(){

let s=searchBox.value.toLowerCase();
let st=statusFilter.value;


filtered=tasks.filter(t=>

(t.title.toLowerCase().includes(s))
&&
(st==""||t.status==st)

);

page=1;

renderTable();

}



/* TABLE */

function renderTable(){

let start=(page-1)*limit;

let end=start+limit;

let rows="";


filtered.slice(start,end).forEach(t=>{

rows+=`

<tr>

<td>${t.id}</td>

<td>${t.title}</td>

<td>${t.project_name}</td>

<td>${t.user_name}</td>

<td>${t.status}</td>

<td>

<button class="btn btn-warning btn-sm"
onclick='openEdit(${JSON.stringify(t)})'>

Edit

</button>

<button class="btn btn-danger btn-sm"
onclick='openDelete(${t.id})'>

Delete

</button>

</td>

</tr>

`;

});


if(rows===""){

rows=`

<tr>

<td colspan="6"
class="text-center text-muted">

No tasks found

</td>

</tr>

`;

}


taskTable.innerHTML=rows;

renderPagination();

}



/* PAGINATION */

function renderPagination(){

let totalPages=Math.ceil(filtered.length/limit);

let buttons="";


for(let i=1;i<=totalPages;i++){

buttons+=`

<li class="page-item ${i==page?'active':''}">

<a class="page-link"
href="#"
onclick="gotoPage(${i})">

${i}

</a>

</li>

`;

}


pagination.innerHTML=buttons;

}



function gotoPage(p){

page=p;

renderTable();

}



/* ADD */

function addTask(){

fetch("/worknest-erp/src/api/tasks.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

title:addTitle.value,
description:addDescription.value,
project_id:addProject.value,
assigned_to:addUser.value,
status:addStatus.value,
due_date:addDue.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Task Added");

loadTasks();

bootstrap.Modal.getInstance(addModal).hide();

}

});

}



/* EDIT */

function openEdit(t){

editId.value=t.id;

editTitle.value=t.title;

editDescription.value=t.description;

editProject.value=t.project_id;

editUser.value=t.assigned_to;

editStatus.value=t.status;

editDue.value=t.due_date;

new bootstrap.Modal(editModal).show();

}



function updateTask(){

fetch(`/worknest-erp/src/api/tasks.php?id=${editId.value}`,{

method:"PUT",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

title:editTitle.value,
description:editDescription.value,
project_id:editProject.value,
assigned_to:editUser.value,
status:editStatus.value,
due_date:editDue.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Updated");

loadTasks();

bootstrap.Modal.getInstance(editModal).hide();

}

});

}



/* DELETE */

function openDelete(id){

deleteId=id;

new bootstrap.Modal(deleteModal).show();

}


function deleteTask(){

fetch(`/worknest-erp/src/api/tasks.php?id=${deleteId}`,{

method:"DELETE"

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Deleted");

loadTasks();

bootstrap.Modal.getInstance(deleteModal).hide();

}

});

}



/* TOAST */

function showToast(msg,error=false){

let toast=document.createElement("div");

toast.className="position-fixed top-0 end-0 p-3";

toast.innerHTML=`

<div class="toast show text-white
${error?'bg-danger':'bg-success'}">

<div class="toast-body">

${msg}

</div>

</div>

`;

document.body.appendChild(toast);

setTimeout(()=>toast.remove(),3000);

}


</script>


<?php require_once dirname(__DIR__,3)."/layout/footer.php"; ?>
