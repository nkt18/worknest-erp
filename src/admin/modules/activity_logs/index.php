<?php

require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";

/* Ensure session */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = new Database();
$conn = $db->connect();

/* =========================
   SEARCH + FILTER
========================= */

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$userFilter = isset($_GET['user_id']) ? intval($_GET['user_id']) : '';

/* =========================
   PAGINATION
========================= */

$limit = 5;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

/* =========================
   BASE QUERY
========================= */

$query = "SELECT activity_logs.*, users.name AS user_name
          FROM activity_logs
          LEFT JOIN users ON activity_logs.user_id = users.id
          WHERE 1=1";

$params = [];
$types = "";

/* Search */
if (!empty($search)) {
    $query .= " AND activity_logs.action LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

/* Filter by user */
if (!empty($userFilter)) {
    $query .= " AND activity_logs.user_id = ?";
    $params[] = $userFilter;
    $types .= "i";
}

/* Count total */
$countQuery = str_replace(
    "SELECT activity_logs.*, users.name AS user_name",
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


$query .= " ORDER BY activity_logs.id ASC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();


$users = $conn->query("SELECT id, name FROM users");


require_once dirname(__DIR__, 3) . "/layout/admin_header.php";
require_once dirname(__DIR__, 3) . "/layout/sidebar_admin.php";
require_once dirname(__DIR__, 3) . "/layout/navbar_admin.php";
?>

<div class="content">

    <div class="mb-4">
        <h2 class="fw-semibold">Activity Logs</h2>
        <p class="text-muted">System activity tracking</p>
    </div>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   placeholder="Search action..."
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">All Users</option>
                <?php while($u = $users->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>"
                        <?= ($userFilter == $u['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($u['name']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="col-md-2">
            <button class="btn btn-primary w-100">Search</button>
        </div>

        <div class="col-md-2">
            <a href="index.php" class="btn btn-secondary w-100">Reset</a>
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
                <tbody>

                <?php if($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['action']) ?></td>
                            <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No activity logs found.
                        </td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <?php if($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    $params = $_GET;
                    $params['page'] = $i;
                    $url = "?" . http_build_query($params);
                    ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="<?= $url ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>
<?php require_once dirname(__DIR__, 3)."/layout/footer.php"; ?>
