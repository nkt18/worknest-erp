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


$query = "SELECT tasks.*, 
                 projects.name AS project_name,
                 users.name AS assigned_name
          FROM tasks
          LEFT JOIN projects ON tasks.project_id = projects.id
          LEFT JOIN users ON tasks.assigned_to = users.id
          WHERE 1=1";

$params = [];
$types = "";

if (!empty($search)) {
    $query .= " AND tasks.title LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

if (!empty($status)) {
    $query .= " AND tasks.status = ?";
    $params[] = $status;
    $types .= "s";
}

$countQuery = str_replace(
    "SELECT tasks.*, 
                 projects.name AS project_name,
                 users.name AS assigned_name",
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

$query .= " ORDER BY tasks.id ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$projects = $conn->query("SELECT id, name FROM projects");

$users = $conn->query("SELECT id, name FROM users");
?>

<!DOCTYPE html>
<html>
<head>
<title>Task Management - WorkNest ERP</title>
<meta charset="UTF-8">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<h2 class="mb-3">Task Management</h2>

<?php if(isset($_GET['added'])): ?>
<div class="alert alert-success" id="alertBox">Task added successfully!</div>
<?php endif; ?>

<?php if(isset($_GET['updated'])): ?>
<div class="alert alert-success" id="alertBox">Task updated successfully!</div>
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
<div class="alert alert-danger" id="alertBox">Task deleted successfully!</div>
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
placeholder="Search task..."
value="<?= htmlspecialchars($search) ?>">
</div>

<div class="col-md-3">
<select name="status" class="form-select">
<option value="">All Status</option>
<option value="pending" <?= ($status=="pending")?'selected':'' ?>>Pending</option>
<option value="in_progress" <?= ($status=="in_progress")?'selected':'' ?>>In Progress</option>
<option value="completed" <?= ($status=="completed")?'selected':'' ?>>Completed</option>
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
+ Add Task
</button>

<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Title</th>
<th>Project</th>
<th>Assigned To</th>
<th>Status</th>
<th>Due Date</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php if($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= htmlspecialchars($row['project_name']) ?></td>
<td><?= htmlspecialchars($row['assigned_name']) ?></td>

<td>
<?php
$badge = "secondary";
if($row['status']=="pending") $badge="warning";
if($row['status']=="in_progress") $badge="primary";
if($row['status']=="completed") $badge="success";
?>
<span class="badge bg-<?= $badge ?>">
<?= ucfirst(str_replace("_"," ",$row['status'])) ?>
</span>
</td>

<td><?= $row['due_date'] ?></td>

<td>
<div class="d-flex gap-2">

<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
data-id="<?= $row['id'] ?>"
data-title="<?= htmlspecialchars($row['title']) ?>"
data-project="<?= $row['project_id'] ?>"
data-assigned="<?= $row['assigned_to'] ?>"
data-status="<?= $row['status'] ?>"
data-due="<?= $row['due_date'] ?>">
Edit
</button>

<a href="delete.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">
Delete
</a>

</div>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
<td colspan="7" class="text-center text-muted">No tasks found.</td>
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
<h5 class="modal-title">Add Task</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="text" name="title" class="form-control mb-2" placeholder="Task Title" required>

<select name="project_id" class="form-select mb-2" required>
<option value="">Select Project</option>
<?php while($p = $projects->fetch_assoc()): ?>
<option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="assigned_to" class="form-select mb-2">
<option value="">Assign To</option>
<?php while($u = $users->fetch_assoc()): ?>
<option value="<?= $u['id'] ?>"><?= $u['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="status" class="form-select mb-2">
<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>
</select>

<label>Due Date</label>
<input type="date" name="due_date" class="form-control mb-2" required>
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
<h5 class="modal-title">Edit Task</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" name="task_id" id="edit_id">
<input type="text" name="title" id="edit_title" class="form-control mb-2" required>

<select name="project_id" id="edit_project" class="form-select mb-2" required>
<option value="">Select Project</option>
<?php
$projects2 = $conn->query("SELECT id, name FROM projects");
while($p = $projects2->fetch_assoc()):
?>
<option value="<?= $p['id'] ?>"><?= $p['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="assigned_to" id="edit_assigned" class="form-select mb-2">
<option value="">Assign To</option>
<?php
$users2 = $conn->query("SELECT id, name FROM users");
while($u = $users2->fetch_assoc()):
?>
<option value="<?= $u['id'] ?>"><?= $u['name'] ?></option>
<?php endwhile; ?>
</select>

<select name="status" id="edit_status" class="form-select mb-2">
<option value="pending">Pending</option>
<option value="in_progress">In Progress</option>
<option value="completed">Completed</option>
</select>

<label>Due Date</label>
<input type="date" name="due_date" id="edit_due" class="form-control mb-2" required>
</div>

<div class="modal-footer">
<button class="btn btn-success">Update</button>
</div>
</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var editModal = document.getElementById('editModal');

editModal.addEventListener('show.bs.modal', function (event) {
var button = event.relatedTarget;

document.getElementById('edit_id').value = button.getAttribute('data-id');
document.getElementById('edit_title').value = button.getAttribute('data-title');
document.getElementById('edit_project').value = button.getAttribute('data-project');
document.getElementById('edit_assigned').value = button.getAttribute('data-assigned');
document.getElementById('edit_status').value = button.getAttribute('data-status');
document.getElementById('edit_due').value = button.getAttribute('data-due');
});
</script>

</body>
</html>