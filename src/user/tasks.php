<?php
require_once __DIR__ . "/../middleware/user.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id'];

if (isset($_POST['task_id']) && isset($_POST['status'])) {
    $taskId = $_POST['task_id'];
    $status = $_POST['status'];

    $stmt = $conn->prepare("
        UPDATE tasks 
        SET status = ?
        WHERE id = ? AND assigned_to = ?
    ");
    $stmt->bind_param("sii", $status, $taskId, $userId);
    $stmt->execute();
}

$stmt = $conn->prepare("
    SELECT t.id, t.title, t.status, p.name as project_name
    FROM tasks t
    JOIN projects p ON t.project_id = p.id
    WHERE t.assigned_to = ?
    ORDER BY t.id DESC
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

    <h4 class="mb-4 fw-semibold">My Tasks</h4>

    <div class="card shadow-sm border-0 p-4">

        <?php if ($result->num_rows > 0): ?>

        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Project</th>
                    <th>Status</th>
                    <th>Change Status</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['project_name']) ?></td>

                    <td>
                        <?php
                        $badgeClass = match($row['status']) {
                            'completed' => 'success',
                            'in_progress' => 'info',
                            'pending' => 'warning',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>">
                            <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
                        </span>
                    </td>

                    <td>
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                            <select name="status" class="form-select form-select-sm">
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                            </select>
                            <button class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>

        <?php else: ?>
            <div class="text-muted">No tasks assigned.</div>
        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>