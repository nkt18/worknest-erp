<?php
require_once __DIR__ . "/../helpers/app.php";
$currentPage = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar">

    <h4 class="text-center py-3 border-bottom">WorkNest ERP</h4>

    <a href="<?= htmlspecialchars(appRoute('admin.dashboard')) ?>"
       class="<?= strpos($currentPage, '/admin/dashboard') !== false ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="<?= htmlspecialchars(appRoute('admin.employees')) ?>"
       class="<?= strpos($currentPage, '/admin/employees') !== false ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i> Employees
    </a>

    <a href="<?= htmlspecialchars(appRoute('admin.projects')) ?>"
       class="<?= strpos($currentPage, '/admin/projects') !== false ? 'active' : '' ?>">
        <i class="bi bi-folder me-2"></i> Projects
    </a>

    <a href="<?= htmlspecialchars(appRoute('admin.tasks')) ?>"
       class="<?= strpos($currentPage, '/admin/tasks') !== false ? 'active' : '' ?>">
        <i class="bi bi-list-task me-2"></i> Tasks
    </a>

    <a href="<?= htmlspecialchars(appRoute('admin.activity_logs')) ?>"
       class="<?= strpos($currentPage, '/admin/activity-logs') !== false ? 'active' : '' ?>">
        <i class="bi bi-clock-history me-2"></i> Activity Logs
    </a>

</div>
