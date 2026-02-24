<?php
require_once __DIR__ . "/../middleware/user.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE assigned_to = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$totalTasks = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE assigned_to = ? AND status='completed'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$completed = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE assigned_to = ? AND status='in_progress'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$inProgress = $stmt->get_result()->fetch_assoc()['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM tasks WHERE assigned_to = ? AND status='pending'");
$stmt->bind_param("i", $userId);
$stmt->execute();
$pending = $stmt->get_result()->fetch_assoc()['total'];

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

    <div class="mb-4">
        <h4 class="fw-semibold">
            Welcome, <?= htmlspecialchars($_SESSION['user_name']); ?>
        </h4>
        <small class="text-muted">Here is your task overview</small>
    </div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <small class="text-muted">Total Tasks</small>
                <h3 class="fw-bold mt-2"><?= $totalTasks ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <small class="text-muted">Completed</small>
                <h3 class="fw-bold text-success mt-2"><?= $completed ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <small class="text-muted">In Progress</small>
                <h3 class="fw-bold text-info mt-2"><?= $inProgress ?></h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0 p-3">
                <small class="text-muted">Pending</small>
                <h3 class="fw-bold text-warning mt-2"><?= $pending ?></h3>
            </div>
        </div>

    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>