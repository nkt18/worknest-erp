<?php

require_once dirname(__DIR__, 3) . "/middleware/admin.php";

require_once dirname(__DIR__, 3) . "/layout/admin_header.php";
require_once dirname(__DIR__, 3) . "/layout/sidebar_admin.php";
require_once dirname(__DIR__, 3) . "/layout/navbar_admin.php";

?>

<div class="content">

<div class="mb-4">
<h2 class="fw-semibold">Activity Logs</h2>
<p class="text-muted">System activity tracking</p>
</div>


<form id="searchForm" class="row g-2 mb-4">

<div class="col-md-4">
<input type="text"
id="search"
class="form-control"
placeholder="Search action...">
</div>


<div class="col-md-3">

<select id="user_id" class="form-select">

<option value="">
All Users
</option>

</select>

</div>


<div class="col-md-2">

<button class="btn btn-primary w-100">
Search
</button>

</div>


<div class="col-md-2">

<button type="button"
onclick="resetSearch()"
class="btn btn-secondary w-100">

Reset

</button>

</div>

</form>



<div class="card dashboard-card">

<div class="card-body p-0">

<table class="table table-hover mb-0 align-middle">

<thead class="table-light">

<tr>

<th>ID</th>
<th>User</th>
<th>Action</th>
<th>Date & Time</th>

</tr>

</thead>

<tbody id="logsTable">

<tr>
<td colspan="4" class="text-center">
Loading...
</td>
</tr>

</tbody>

</table>

</div>

</div>


<div id="pagination"
class="mt-4 d-flex justify-content-center">

</div>

</div>



<script>

let currentPage=1;

loadLogs();



function loadLogs(page=1){

currentPage=page;

let search=
document.getElementById("search").value;

let user=
document.getElementById("user_id").value;


fetch(
`/worknest-erp/src/api/activity_logs.php?page=${page}&search=${search}&user_id=${user}`
)

.then(res=>res.json())

.then(data=>{

let rows="";


if(data.data.length===0){

rows=`

<tr>
<td colspan="4"
class="text-center text-muted">

No logs found

</td>
</tr>

`;

}else{

data.data.forEach(log=>{

rows+=`

<tr>

<td>${log.id}</td>

<td>${log.user_name??''}</td>

<td>${log.action}</td>

<td>${log.created_at}</td>

</tr>

`;

});

}

document.getElementById("logsTable")
.innerHTML=rows;


createPagination(data.totalPages);

});

}



document
.getElementById("searchForm")
.addEventListener("submit",function(e){

e.preventDefault();

loadLogs();

});



function createPagination(total){

let html="";

for(let i=1;i<=total;i++){

html+=`

<button
class="btn btn-sm ${i==currentPage?'btn-primary':'btn-light'} me-1"
onclick="loadLogs(${i})">

${i}

</button>

`;

}

document.getElementById("pagination")
.innerHTML=html;

}



function resetSearch(){

document.getElementById("search").value="";

document.getElementById("user_id").value="";

loadLogs();

}

</script>

<?php require_once dirname(__DIR__, 3)."/layout/footer.php"; ?>
