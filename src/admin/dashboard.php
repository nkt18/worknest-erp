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

require_once __DIR__ . "/../layout/admin_header.php";
require_once __DIR__ . "/../layout/sidebar_admin.php";
require_once __DIR__ . "/../layout/navbar_admin.php";
?>

<div class="content">

    <div class="mb-4">
        <h2 class="fw-semibold">Dashboard Overview</h2>
        <p class="text-muted">Welcome, <?= $_SESSION['name'] ?? 'Admin'; ?> </p>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Projects</p>
                    <div class="stat-number"><?= $totalProjects ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Tasks</p>
                    <div class="stat-number"><?= $totalTasks ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Completed Tasks</p>
                    <div class="stat-number text-success"><?= $completedTasks ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Pending Tasks</p>
                    <div class="stat-number text-warning"><?= $pendingTasks ?></div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-4 mt-1">

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">In Progress</p>
                    <div class="stat-number text-info"><?= $inProgressTasks ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Users</p>
                    <div class="stat-number"><?= $totalUsers ?></div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card">
                <div class="card-body">
                    <p class="text-muted mb-1">Total Employees</p>
                    <div class="stat-number"><?= $totalEmployees ?></div>
                </div>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>