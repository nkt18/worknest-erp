<?php

require_once __DIR__ . "/../middleware/user.php";

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";

?>

<div class="content">

<div class="mb-4">
<h4 class="fw-semibold">My Tasks</h4>
<small class="text-muted">Tasks assigned to you</small>
</div>

<div class="card shadow-sm border-0 p-4">

<!-- SEARCH -->

<div class="row mb-3">

<div class="col-md-6">

<input type="text"
id="searchInput"
class="form-control"
placeholder="Search task...">

</div>

</div>

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>

<th>Title</th>
<th>Project</th>
<th>Status</th>
<th>Change Status</th>

</tr>

</thead>

<tbody id="taskTable">

<tr>
<td colspan="4" class="text-center text-muted">

Loading tasks...

</td>
</tr>

</tbody>

</table>

<!-- PAGINATION -->

<nav>

<ul class="pagination justify-content-center"
id="pagination">

</ul>

</nav>

</div>

</div>

<script>

let allTasks=[];
let currentPage=1;
let perPage=5;

/* LOAD TASKS */

fetch("/worknest-erp/src/api/tasks.php")

.then(response=>response.json())

.then(data=>{

allTasks=data.data;

renderTasks();

})

.catch(()=>showError());

/* RENDER TASKS */

function renderTasks(){

let search=document
.getElementById("searchInput")
.value.toLowerCase();

let filtered=allTasks.filter(t=>

t.title.toLowerCase().includes(search)

);

let start=(currentPage-1)*perPage;
let end=start+perPage;

let pageData=filtered.slice(start,end);

let rows="";

if(pageData.length===0){

rows=`
<tr>
<td colspan="4"
class="text-center text-muted">

No tasks found

</td>
</tr>
`;

}
else{

pageData.forEach(task=>{

let badgeColor="secondary";

if(task.status==="completed") badgeColor="success";
if(task.status==="in_progress") badgeColor="info";
if(task.status==="pending") badgeColor="warning";

rows+=`

<tr>

<td>${task.title}</td>

<td>${task.project_name}</td>

<td>

<span id="badge-${task.id}"
class="badge bg-${badgeColor}">

${task.status.replace("_"," ").toUpperCase()}

</span>

</td>

<td>

<select class="form-select form-select-sm task-status"
data-id="${task.id}">

<option value="pending"
${task.status==="pending"?"selected":""}>
Pending
</option>

<option value="in_progress"
${task.status==="in_progress"?"selected":""}>
In Progress
</option>

<option value="completed"
${task.status==="completed"?"selected":""}>
Completed
</option>

</select>

</td>

</tr>

`;

});


}

document
.getElementById("taskTable")
.innerHTML=rows;

attachEvents();

renderPagination(filtered.length);

}

/* PAGINATION */

function renderPagination(total){

let pages=Math.ceil(total/perPage);

let html="";

for(let i=1;i<=pages;i++){

html+=`

<li class="page-item
${i==currentPage?'active':''}">

<a class="page-link"
href="#"
onclick="gotoPage(${i})">

${i}

</a>

</li>

`;

}

document
.getElementById("pagination")
.innerHTML=html;

}

function gotoPage(page){

currentPage=page;

renderTasks();

}

/* SEARCH EVENT */

document
.getElementById("searchInput")
.addEventListener("keyup",()=>{

currentPage=1;

renderTasks();

});

/* ATTACH UPDATE EVENTS */

function attachEvents(){

document
.querySelectorAll(".task-status")

.forEach(select=>{

select.addEventListener("change",updateTask);

});

}

/* UPDATE TASK */

function updateTask(){

let taskId=this.dataset.id;
let status=this.value;

fetch(`/worknest-erp/src/api/tasks.php?id=${taskId}`,{

method:"PUT",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({
status:status
})

})

.then(response=>response.json())

.then(data=>{

if(data.success){

let badge=document
.getElementById("badge-"+taskId);

let color="secondary";

if(status==="completed") color="success";
if(status==="in_progress") color="info";
if(status==="pending") color="warning";

badge.className="badge bg-"+color;

badge.innerText=status
.replace("_"," ")
.toUpperCase();

showToast("Task Updated");

}
else{

showToast("Update Failed",true);

}

});

}

/* ERROR */

function showError(){

document
.getElementById("taskTable")
.innerHTML=`

<tr>

<td colspan="4"
class="text-danger text-center">

Error loading tasks

</td>

</tr>

`;

}

/* TOAST */

function showToast(message,error=false){

let toast=document.createElement("div");

toast.className=
"position-fixed top-0 end-0 p-3";

toast.innerHTML=`

<div class="toast show
text-white
${error?'bg-danger':'bg-success'}">

<div class="toast-body">

${message}

</div>

</div>

`;

document.body.appendChild(toast);

setTimeout(()=>toast.remove(),3000);

}

</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
