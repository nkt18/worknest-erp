<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/layout/admin_header.php";
require_once dirname(__DIR__,3)."/layout/sidebar_admin.php";
require_once dirname(__DIR__,3)."/layout/navbar_admin.php";

?>

<div class="content">

<h4 class="mb-4 fw-semibold">Project Management</h4>

<div class="card dashboard-card p-4">

<div class="row mb-3">

<div class="col-md-4">

<input type="text"
id="searchBox"
class="form-control"
placeholder="Search project">

</div>

<div class="col-md-3">

<select id="statusFilter" class="form-select">

<option value="">All Status</option>
<option value="Active">Active</option>
<option value="On Hold">On Hold</option>
<option value="Completed">Completed</option>

</select>

</div>

<div class="col-md-3">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addModal">

+ Add Project

</button>

</div>

</div>


<table class="table table-hover">

<thead class="table-light">

<tr>

<th>ID</th>
<th>Name</th>
<th>Status</th>
<th>Start</th>
<th>End</th>
<th width="150">Actions</th>

</tr>

</thead>

<tbody id="projectTable">

<tr>

<td colspan="6" class="text-center text-muted">

Loading projects...

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

<h5>Add Project</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input id="addName"
class="form-control mb-2"
placeholder="Project Name">

<textarea id="addDescription"
class="form-control mb-2"
placeholder="Description"></textarea>

<select id="addStatus"
class="form-select mb-2">

<option value="Active">Active</option>
<option value="On Hold">On Hold</option>
<option value="Completed">Completed</option>

</select>

<label>Start Date</label>

<input id="addStart"
type="date"
class="form-control mb-2">

<label>End Date</label>

<input id="addEnd"
type="date"
class="form-control mb-2">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="addProject()">

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

<h5>Edit Project</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input type="hidden" id="editId">

<input id="editName"
class="form-control mb-2">

<textarea id="editDescription"
class="form-control mb-2"></textarea>

<select id="editStatus"
class="form-select mb-2">

<option value="Active">Active</option>
<option value="On Hold">On Hold</option>
<option value="Completed">Completed</option>

</select>

<label>Start Date</label>

<input id="editStart"
type="date"
class="form-control mb-2">

<label>End Date</label>

<input id="editEnd"
type="date"
class="form-control mb-2">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="updateProject()">

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

Delete this project?

</div>

<div class="modal-footer">

<button class="btn btn-danger"
onclick="deleteProject()">

Delete

</button>

</div>

</div>

</div>

</div>



<script>

let projects=[];
let filtered=[];
let page=1;
let limit=5;
let deleteId=null;


loadProjects();


function loadProjects(){

fetch("/worknest-erp/src/api/projects.php")

.then(res=>res.json())

.then(data=>{

projects=data.data;

applyFilters();

});

}


document.getElementById("searchBox")
.addEventListener("keyup",applyFilters);

document.getElementById("statusFilter")
.addEventListener("change",applyFilters);



function applyFilters(){

let search=
document.getElementById("searchBox")
.value.toLowerCase();

let status=
document.getElementById("statusFilter")
.value;


filtered=projects.filter(p=>{

return(

p.name.toLowerCase().includes(search)

&&

(status==""||p.status==status)

);

});


page=1;

renderTable();

}



function renderTable(){

let start=(page-1)*limit;

let end=start+limit;

let rows="";


filtered.slice(start,end)
.forEach(p=>{

rows+=`

<tr>

<td>${p.id}</td>

<td>${p.name}</td>

<td>${p.status}</td>

<td>${p.start_date}</td>

<td>${p.end_date}</td>

<td>

<button class="btn btn-warning btn-sm"

onclick='openEdit(${JSON.stringify(p)})'>

Edit

</button>

<button class="btn btn-danger btn-sm"

onclick='openDelete(${p.id})'>

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

No projects found

</td>

</tr>

`;

}


document.getElementById("projectTable")
.innerHTML=rows;

renderPagination();

}



function renderPagination(){

let totalPages=
Math.ceil(filtered.length/limit);

let buttons="";


for(let i=1;i<=totalPages;i++){

buttons+=`

<li class="page-item
${i==page?'active':''}">

<a class="page-link"
href="#"

onclick="gotoPage(${i})">

${i}

</a>

</li>

`;

}


document.getElementById("pagination")
.innerHTML=buttons;

}


function gotoPage(p){

page=p;

renderTable();

}



function addProject(){

fetch("/worknest-erp/src/api/projects.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:addName.value,
description:addDescription.value,
status:addStatus.value,
start_date:addStart.value,
end_date:addEnd.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Project Added");

loadProjects();

bootstrap.Modal.getInstance(
document.getElementById("addModal"))
.hide();

}

});

}



function openEdit(p){

editId.value=p.id;
editName.value=p.name;
editDescription.value=p.description;
editStatus.value=p.status;
editStart.value=p.start_date;
editEnd.value=p.end_date;

new bootstrap.Modal(
document.getElementById("editModal"))
.show();

}



function updateProject(){

fetch(`/worknest-erp/src/api/projects.php?id=${editId.value}`,{

method:"PUT",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:editName.value,
description:editDescription.value,
status:editStatus.value,
start_date:editStart.value,
end_date:editEnd.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Updated");

loadProjects();

bootstrap.Modal.getInstance(
document.getElementById("editModal"))
.hide();

}

});

}



function openDelete(id){

deleteId=id;

new bootstrap.Modal(
document.getElementById("deleteModal"))
.show();

}



function deleteProject(){

fetch(`/worknest-erp/src/api/projects.php?id=${deleteId}`,{

method:"DELETE"

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Deleted");

loadProjects();

bootstrap.Modal.getInstance(
document.getElementById("deleteModal"))
.hide();

}

});

}



function showToast(msg,error=false){

let toast=document.createElement("div");

toast.className=
"position-fixed top-0 end-0 p-3";

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
