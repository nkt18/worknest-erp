<?php

require_once __DIR__ . "/../middleware/admin.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$totalProjects = $conn->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
$totalTasks = $conn->query("SELECT COUNT(*) as total FROM tasks")->fetch_assoc()['total'];
$totalUsers = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$totalEmployees = $conn->query("SELECT COUNT(*) as total FROM employees")->fetch_assoc()['total'];

$completedTasks = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE status='completed'")->fetch_assoc()['total'];
$pendingTasks = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE status='pending'")->fetch_assoc()['total'];
$inProgressTasks = $conn->query("SELECT COUNT(*) as total FROM tasks WHERE status='in_progress'")->fetch_assoc()['total'];

$logs = $conn->query("
SELECT a.action,a.created_at,u.name
FROM activity_logs a
LEFT JOIN users u ON a.user_id=u.id
ORDER BY a.id DESC
LIMIT 5
");

require_once __DIR__ . "/../layout/admin_header.php";
require_once __DIR__ . "/../layout/sidebar_admin.php";
require_once __DIR__ . "/../layout/navbar_admin.php";

?>

<div class="content">

<div class="mb-4">

<h2 class="fw-semibold">
Dashboard Overview
</h2>

<p class="text-muted">

Welcome,
<?= htmlspecialchars($_SESSION['user_name']) ?>

</p>

</div>

<!-- STATS CARDS -->

<div class="row g-4">


<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Total Projects
</p>

<div class="stat-number">
<?= $totalProjects ?>
</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Total Tasks
</p>

<div class="stat-number">
<?= $totalTasks ?>
</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Completed Tasks
</p>

<div class="stat-number text-success">
<?= $completedTasks ?>
</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Pending Tasks
</p>

<div class="stat-number text-warning">
<?= $pendingTasks ?>
</div>

</div>

</div>

</div>

</div>

<div class="row g-4 mt-1">

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
In Progress
</p>

<div class="stat-number text-info">
<?= $inProgressTasks ?>
</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Total Users
</p>

<div class="stat-number">
<?= $totalUsers ?>
</div>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card dashboard-card">

<div class="card-body">

<p class="text-muted mb-1">
Total Employees
</p>

<div class="stat-number">
<?= $totalEmployees ?>
</div>

</div>

</div>

</div>

</div>

<!-- RECENT ACTIVITY -->

<div class="card dashboard-card mt-4">

<div class="card-body">

<h5 class="mb-3">
Recent Activity
</h5>

<table class="table align-middle">

<thead>

<tr>

<th>User</th>
<th>Action</th>
<th>Date</th>

</tr>

</thead>

<tbody>

<?php while($log=$logs->fetch_assoc()): ?>

<tr>

<td>

<?= htmlspecialchars($log['name']) ?>

</td>

<td>

<?= htmlspecialchars($log['action']) ?>

</td>

<td>

<?= date("d M Y H:i",
strtotime($log['created_at'])) ?>

</td>

</tr>

<?php endwhile; ?>


</tbody>

</table>

</div>

</div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>