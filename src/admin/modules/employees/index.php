<?php

require_once dirname(__DIR__,3)."/middleware/admin.php";

require_once dirname(__DIR__,3)."/layout/admin_header.php";
require_once dirname(__DIR__,3)."/layout/sidebar_admin.php";
require_once dirname(__DIR__,3)."/layout/navbar_admin.php";

?>

<div class="content">

<h4 class="mb-4 fw-semibold">Employee Management</h4>

<div class="card dashboard-card p-4">

<div class="row mb-3">

<div class="col-md-4">

<input type="text"
id="searchBox"
class="form-control"
placeholder="Search employee">

</div>

<div class="col-md-3">

<select id="departmentFilter" class="form-select">

<option value="">All Departments</option>
<option value="HR">HR</option>
<option value="IT">IT</option>
<option value="Finance">Finance</option>

</select>

</div>

<div class="col-md-3">

<button class="btn btn-primary"
data-bs-toggle="modal"
data-bs-target="#addModal">

+ Add Employee

</button>

</div>

</div>


<table class="table table-hover">

<thead class="table-light">

<tr>

<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Designation</th>
<th>Phone</th>
<th>Department</th>
<th width="150">Actions</th>

</tr>

</thead>

<tbody id="employeeTable">

<tr>

<td colspan="7" class="text-center text-muted">

Loading employees...

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

<h5>Add Employee</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input id="addName"
class="form-control mb-2"
placeholder="Name">

<input id="addEmail"
class="form-control mb-2"
placeholder="Email">

<input id="addDesignation"
class="form-control mb-2"
placeholder="Designation">

<input id="addPhone"
class="form-control mb-2"
placeholder="Phone">

<input id="addDepartment"
class="form-control mb-2"
placeholder="Department">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="addEmployee()">

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

<h5>Edit Employee</h5>

<button class="btn-close"
data-bs-dismiss="modal"></button>

</div>

<div class="modal-body">

<input type="hidden" id="editId">

<input id="editName"
class="form-control mb-2">

<input id="editEmail"
class="form-control mb-2">

<input id="editDesignation"
class="form-control mb-2">

<input id="editPhone"
class="form-control mb-2">

<input id="editDepartment"
class="form-control mb-2">

</div>

<div class="modal-footer">

<button class="btn btn-success"
onclick="updateEmployee()">

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

Delete this employee?

</div>

<div class="modal-footer">

<button class="btn btn-danger"
onclick="deleteEmployee()">

Delete

</button>

</div>

</div>

</div>

</div>



<script>


let employees=[];
let filtered=[];
let page=1;
let limit=5;
let deleteId=null;


/* LOAD */

loadEmployees();


function loadEmployees(){

fetch("/worknest-erp/src/api/employees.php")

.then(res=>res.json())

.then(data=>{

employees=data.data;

applyFilters();

});

}


/* SEARCH */

document.getElementById("searchBox")
.addEventListener("keyup",applyFilters);


document.getElementById("departmentFilter")
.addEventListener("change",applyFilters);



function applyFilters(){

let search=
document.getElementById("searchBox")
.value.toLowerCase();

let dept=
document.getElementById("departmentFilter")
.value;

filtered=employees.filter(emp=>{

return(

(emp.name.toLowerCase().includes(search)
||

emp.email.toLowerCase().includes(search))

&&

(dept==""||emp.department==dept)

);

});


page=1;

renderTable();

}


/* TABLE */


function renderTable(){

let start=(page-1)*limit;

let end=start+limit;

let rows="";


filtered.slice(start,end)
.forEach(emp=>{

rows+=`

<tr>

<td>${emp.id}</td>

<td>${emp.name}</td>

<td>${emp.email}</td>

<td>${emp.designation}</td>

<td>${emp.phone}</td>

<td>${emp.department}</td>

<td>

<button class="btn btn-warning btn-sm"

onclick='openEdit(${JSON.stringify(emp)})'>

Edit

</button>

<button class="btn btn-danger btn-sm"

onclick='openDelete(${emp.id})'>

Delete

</button>

</td>

</tr>

`;

});


if(rows===""){

rows=`

<tr>

<td colspan="7"
class="text-center text-muted">

No employees found

</td>

</tr>

`;

}


document.getElementById("employeeTable")
.innerHTML=rows;

renderPagination();

}



/* PAGINATION */


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



/* ADD */


function addEmployee(){

fetch("/worknest-erp/src/api/employees.php",{

method:"POST",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:addName.value,
email:addEmail.value,
designation:addDesignation.value,
phone:addPhone.value,
department:addDepartment.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Employee Added");

loadEmployees();

bootstrap.Modal.getInstance(
document.getElementById("addModal"))
.hide();

}else{

showToast(data.message,true);

}

});

}



/* EDIT */


function openEdit(emp){

editId.value=emp.id;

editName.value=emp.name;

editEmail.value=emp.email;

editDesignation.value=emp.designation;

editPhone.value=emp.phone;

editDepartment.value=emp.department;

new bootstrap.Modal(
document.getElementById("editModal"))
.show();

}



function updateEmployee(){

fetch(`/worknest-erp/src/api/employees.php?id=${editId.value}`,{

method:"PUT",

headers:{
"Content-Type":"application/json"
},

body:JSON.stringify({

name:editName.value,
email:editEmail.value,
designation:editDesignation.value,
phone:editPhone.value,
department:editDepartment.value

})

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Updated");

loadEmployees();

bootstrap.Modal.getInstance(
document.getElementById("editModal"))
.hide();

}

});

}



/* DELETE */


function openDelete(id){

deleteId=id;

new bootstrap.Modal(
document.getElementById("deleteModal"))
.show();

}



function deleteEmployee(){

fetch(`/worknest-erp/src/api/employees.php?id=${deleteId}`,{

method:"DELETE"

})

.then(res=>res.json())

.then(data=>{

if(data.success){

showToast("Deleted");

loadEmployees();

bootstrap.Modal.getInstance(
document.getElementById("deleteModal"))
.hide();

}

});

}



/* TOAST */


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
