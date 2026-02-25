<?php
require_once __DIR__ . "/../middleware/user.php";

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

<div class="mb-4">
<h4 class="fw-semibold">My Projects</h4>
<small class="text-muted">Projects assigned to you</small>
</div>


<div class="card shadow-sm border-0 p-4">

<!-- SEARCH BAR -->

<div class="row mb-3">

<div class="col-md-6">

<input type="text"
id="searchInput"
class="form-control"
placeholder="Search project...">

</div>

</div>

<table class="table table-hover align-middle">

<thead class="table-light">

<tr>
<th>ID</th>
<th>Name</th>
<th>Description</th>
<th>Status</th>
</tr>

</thead>

<tbody id="projectTable">

<tr>
<td colspan="4"
class="text-center text-muted">

Loading projects...

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

let allProjects=[];
let currentPage=1;
let perPage=5;

/* LOAD PROJECTS */

fetch("/worknest-erp/src/api/projects.php")

.then(res=>res.json())

.then(data=>{

if(!data.success){

showError();
return;

}

allProjects=data.data;

renderProjects();

})

.catch(()=>showError());

/* RENDER PROJECTS */

function renderProjects(){

let search=document
.getElementById("searchInput")
.value.toLowerCase();

let filtered=allProjects.filter(p=>

p.name.toLowerCase().includes(search)

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

No projects found

</td>
</tr>
`;

}
else{

pageData.forEach(p=>{

let badge="secondary";

if(p.status=="active") badge="success";
if(p.status=="completed") badge="primary";
if(p.status=="on_hold") badge="warning";


rows+=`

<tr>

<td>${p.id}</td>

<td>${p.name}</td>

<td>${p.description ?? ''}</td>

<td>

<span class="badge bg-${badge}">

${p.status}

</span>

</td>

</tr>

`;

});

}

document
.getElementById("projectTable")
.innerHTML=rows;

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

renderProjects();

}

/* SEARCH EVENT */

document
.getElementById("searchInput")
.addEventListener("keyup",()=>{

currentPage=1;

renderProjects();

});

/* ERROR HANDLER */

function showError(){

document
.getElementById("projectTable")
.innerHTML=`

<tr>
<td colspan="4"
class="text-center text-danger">

Error loading projects

</td>
</tr>

`;

}

</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>
