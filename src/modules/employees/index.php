<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->connect();

/* ================= SEARCH + FILTER + PAGINATION ================= */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$department = isset($_GET['department']) ? trim($_GET['department']) : '';

$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$query = "SELECT 
            employees.id, 
            users.name, 
            users.email, 
            employees.designation,
            employees.phone,
            employees.department
          FROM employees
          JOIN users ON employees.user_id = users.id
          WHERE 1=1";

$params = [];
$types = "";

/* Search */
if (!empty($search)) {
    $query .= " AND (users.name LIKE ? OR users.email LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= "ss";
}

/* Filter */
if (!empty($department)) {
    $query .= " AND employees.department = ?";
    $params[] = $department;
    $types .= "s";
}

/* Count total rows */
$countQuery = "SELECT COUNT(*) as total
               FROM employees
               JOIN users ON employees.user_id = users.id
               WHERE 1=1";

if (!empty($search)) {
    $countQuery .= " AND (users.name LIKE ? OR users.email LIKE ?)";
}
if (!empty($department)) {
    $countQuery .= " AND employees.department = ?";
}

$countStmt = $conn->prepare($countQuery);

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

/* Add pagination */
$query .= " ORDER BY employees.id ASC LIMIT ? OFFSET ?";
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
    <title>Employee Management - WorkNest ERP</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-4">

<!-- SUCCESS ALERTS -->
<?php if(isset($_GET['updated'])): ?>
<div class="alert alert-success" id="alertBox">Employee updated successfully!</div>
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
<div class="alert alert-danger" id="alertBox">Employee deleted successfully!</div>
<?php endif; ?>

<script>
setTimeout(function(){
    var alertBox = document.getElementById('alertBox');
    if(alertBox){ alertBox.remove(); }
}, 3000);
</script>

<h2 class="mb-3">Employee Management</h2>

<!-- SEARCH + FILTER -->
<form method="GET" class="row g-2 mb-3">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control"
               placeholder="Search by name or email"
               value="<?= htmlspecialchars($search) ?>">
    </div>

    <div class="col-md-3">
        <select name="department" class="form-select">
            <option value="">All Departments</option>
            <option value="HR" <?= ($department=="HR")?'selected':'' ?>>HR</option>
            <option value="IT" <?= ($department=="IT")?'selected':'' ?>>IT</option>
            <option value="Finance" <?= ($department=="Finance")?'selected':'' ?>>Finance</option>
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
+ Add Employee
</button>

<table class="table table-hover table-bordered align-middle">
<thead class="table-dark">
<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Designation</th>
<th>Phone</th>
<th>Department</th>
<th>Actions</th>
</tr>
</thead>
<tbody>

<?php if($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= htmlspecialchars($row['designation']) ?></td>
<td><?= htmlspecialchars($row['phone']) ?></td>
<td><?= htmlspecialchars($row['department']) ?></td>
<td>
<div class="d-flex gap-2">

<button class="btn btn-warning btn-sm"
data-bs-toggle="modal"
data-bs-target="#editModal"
data-id="<?= $row['id'] ?>"
data-name="<?= htmlspecialchars($row['name']) ?>"
data-email="<?= htmlspecialchars($row['email']) ?>"
data-designation="<?= htmlspecialchars($row['designation']) ?>"
data-phone="<?= htmlspecialchars($row['phone']) ?>"
data-department="<?= htmlspecialchars($row['department']) ?>">
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
<td colspan="7" class="text-center text-muted">No employees found.</td>
</tr>
<?php endif; ?>

</tbody>
</table>

<!-- PAGINATION -->
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

<!-- ADD MODAL -->
<div class="modal fade" id="addModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" action="store.php">
<div class="modal-header">
<h5 class="modal-title">Add Employee</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<input type="text" name="name" class="form-control mb-2" placeholder="Name" required>
<input type="email" name="email" class="form-control mb-2" placeholder="Email" required>
<input type="text" name="designation" class="form-control mb-2" placeholder="Designation" required>
<input type="text" name="phone" class="form-control mb-2" placeholder="Phone" required>
<input type="text" name="department" class="form-control mb-2" placeholder="Department" required>
</div>
<div class="modal-footer">
<button class="btn btn-success">Save</button>
</div>
</form>
</div>
</div>
</div>

<!-- DELETE MODAL -->
<div class="modal fade" id="deleteModal">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Confirm Delete</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
Are you sure you want to delete this employee?
</div>
<div class="modal-footer">
<a href="#" id="confirmDeleteBtn" class="btn btn-danger">Yes Delete</a>
</div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
var deleteModal=document.getElementById('deleteModal');
deleteModal.addEventListener('show.bs.modal',function(event){
var button=event.relatedTarget;
var id=button.getAttribute('data-id');
document.getElementById('confirmDeleteBtn').setAttribute('href','delete.php?id='+id);
});
</script>

</body>
</html>