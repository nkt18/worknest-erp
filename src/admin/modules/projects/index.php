<?php

require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";
$db = new Database();
$conn = $db->connect();


$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';


$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$query = "SELECT projects.*, users.name AS created_by_name
          FROM projects
          LEFT JOIN users ON projects.created_by = users.id
          WHERE 1=1";

$params = [];
$types = "";


if (!empty($search)) {
    $query .= " AND projects.name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($status)) {
    $query .= " AND projects.status = ?";
    $params[] = $status;
    $types .= "s";
}

$countQuery = str_replace(
    "SELECT projects.*, users.name AS created_by_name",
    "SELECT COUNT(*) as total",
    $query
);

$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$query .= " ORDER BY projects.id ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Project Management - WorkNest ERP</title>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2 class="mb-3">Project Management</h2>


<?php if(isset($_GET['added'])): ?>
<div class="alert alert-success" id="alertBox">Project added successfully!</div>
<?php endif; ?>

<?php if(isset($_GET['updated'])): ?>
<div class="alert alert-success" id="alertBox">Project updated successfully!</div>
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
<div class="alert alert-danger" id="alertBox">Project deleted successfully!</div>
<?php endif; ?>

<script>
setTimeout(function(){
    var alertBox = document.getElementById('alertBox');
    if(alertBox){ alertBox.remove(); }
},3000);
</script>


<form method="GET" class="row g-2 mb-3">
<div class="col-md-4">
<input type="text" name="search" class="form-control"
placeholder="Search project..."
value="<?= htmlspecialchars($search) ?>">
</div>

<div class="col-md-3">
<select name="status" class="form-select">
<option value="">All Status</option>
<option value="Active" <?= ($status=="Active")?'selected':'' ?>>Active</option>
<option value="On Hold" <?= ($status=="On Hold")?'selected':'' ?>>On Hold</option>
<option value="Completed" <?= ($status=="Completed")?'selected':'' ?>>Completed</option>
</select>
</div>

<div class="col-md-2">
<button class="btn btn-primary w-100">Search</button>
</div>

<div class="col-md-2">
<a href="index.php" class="btn btn-secondary w-100">Reset</a>
</div>
</form>

<a href="../../dashboard.php" class="btn btn-secondary mb-3">← Back</a>

<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addModal">
+ Add Project
</button>

<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Status</th>
<th>Start</th>
<th>End</th>
<th>Created By</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php if($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>

<td>
<?php
$badge = "secondary";
if($row['status']=="Active") $badge="success";
if($row['status']=="On Hold") $badge="warning";
if($row['status']=="Completed") $badge="primary";
?>
<span class="badge bg-<?= $badge ?>">
<?= $row['status'] ?>
</span>
</td>

<td><?= $row['start_date'] ?></td>
<td><?= $row['end_date'] ?></td>
<td><?= htmlspecialchars($row['created_by_name']) ?></td>

<td>
<div class="d-flex gap-2">
<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
data-id="<?= $row['id'] ?>"
data-name="<?= htmlspecialchars($row['name']) ?>"
data-description="<?= htmlspecialchars($row['description']) ?>"
data-start="<?= $row['start_date'] ?>"
data-end="<?= $row['end_date'] ?>"
data-status="<?= $row['status'] ?>">
Edit
</button>

<button class="btn btn-danger btn-sm"
data-bs-toggle="modal"
data-bs-target="#deleteModal"
data-id="<?= $row['id'] ?>">
Delete
</button>
</div>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center text-muted">No projects found.</td>
</tr>
<?php endif; ?>

</tbody>
</table>


<?php if($totalPages > 1): ?>
<nav>
<ul class="pagination justify-content-center">
<?php for($i=1;$i<=$totalPages;$i++): ?>
<?php
$params = $_GET;
$params['page']=$i;
$url="?".http_build_query($params);
?>
<li class="page-item <?= ($i==$page)?'active':'' ?>">
<a class="page-link" href="<?= $url ?>"><?= $i ?></a>
</li>
<?php endfor; ?>
</ul>
</nav>
<?php endif; ?>

</div>


<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" action="store.php">
<div class="modal-header">
<h5 class="modal-title">Add Project</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="text" name="name" class="form-control mb-2" placeholder="Project Name" required>
<textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
<label>Status</label>
<select name="status" class="form-select mb-2">
<option value="Active">Active</option>
<option value="On Hold">On Hold</option>
<option value="Completed">Completed</option>
</select>
<label>Start Date</label>
<input type="date" name="start_date" class="form-control mb-2" required>
<label>End Date</label>
<input type="date" name="end_date" class="form-control mb-2" required>
</div>
<div class="modal-footer">
<button class="btn btn-success">Save</button>
</div>
</form>
</div>
</div>
</div>


<div class="modal fade" id="editModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" action="update.php">
<div class="modal-header">
<h5 class="modal-title">Edit Project</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="hidden" name="project_id" id="edit_id">
<input type="text" name="name" id="edit_name" class="form-control mb-2" required>
<textarea name="description" id="edit_description" class="form-control mb-2"></textarea>
<label>Status</label>
<select name="status" id="edit_status" class="form-select mb-2">
<option value="Active">Active</option>
<option value="On Hold">On Hold</option>
<option value="Completed">Completed</option>
</select>
<label>Start Date</label>
<input type="date" name="start_date" id="edit_start" class="form-control mb-2" required>
<label>End Date</label>
<input type="date" name="end_date" id="edit_end" class="form-control mb-2" required>
</div>
<div class="modal-footer">
<button class="btn btn-success">Update</button>
</div>
</form>
</div>
</div>
</div>


<div class="modal fade" id="deleteModal">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Confirm Delete</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">Are you sure?</div>
<div class="modal-footer">
<a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes Delete</a>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var editModal=document.getElementById('editModal');
editModal.addEventListener('show.bs.modal',function(event){
var button=event.relatedTarget;
document.getElementById('edit_id').value=button.getAttribute('data-id');
document.getElementById('edit_name').value=button.getAttribute('data-name');
document.getElementById('edit_description').value=button.getAttribute('data-description');
document.getElementById('edit_start').value=button.getAttribute('data-start');
document.getElementById('edit_end').value=button.getAttribute('data-end');
document.getElementById('edit_status').value=button.getAttribute('data-status');
});

var deleteModal=document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal',function(event){
var button=event.relatedTarget;
var id=button.getAttribute('data-id');
document.getElementById('confirmDeleteBtn').setAttribute('href','delete.php?id='+id);
});
</script>

</body>
</html>