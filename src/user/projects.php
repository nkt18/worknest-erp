<?php
require_once __DIR__ . "/../middleware/user.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT DISTINCT p.id, p.name, p.description
    FROM projects p
    JOIN tasks t ON p.id = t.project_id
    WHERE t.assigned_to = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

    <h4 class="mb-4 fw-semibold">My Projects</h4>

    <div class="card shadow-sm border-0 p-4">

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Project Name</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                    </tr>
                <?php endwhile; ?>

                </tbody>
            </table>
        <?php else: ?>
            <div class="text-muted">No projects assigned.</div>
        <?php endif; ?>

    </div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>