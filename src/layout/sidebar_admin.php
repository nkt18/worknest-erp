<?php
$currentPage = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar">

    <h4 class="text-center py-3 border-bottom">WorkNest ERP</h4>

    <a href="/worknest-erp/src/admin/dashboard.php"
       class="<?= strpos($currentPage, '/admin/dashboard.php') !== false ? 'active' : '' ?>">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="/worknest-erp/src/admin/modules/employees/index.php"
       class="<?= strpos($currentPage, '/employees/') !== false ? 'active' : '' ?>">
        <i class="bi bi-people me-2"></i> Employees
    </a>

    <a href="/worknest-erp/src/admin/modules/projects/index.php"
       class="<?= strpos($currentPage, '/projects/') !== false ? 'active' : '' ?>">
        <i class="bi bi-folder me-2"></i> Projects
    </a>

    <a href="/worknest-erp/src/admin/modules/tasks/index.php"
       class="<?= strpos($currentPage, '/tasks/') !== false ? 'active' : '' ?>">
        <i class="bi bi-list-task me-2"></i> Tasks
    </a>

    <a href="/worknest-erp/src/admin/modules/activity_logs/index.php"
       class="<?= strpos($currentPage, '/activity_logs/') !== false ? 'active' : '' ?>">
        <i class="bi bi-clock-history me-2"></i> Activity Logs
    </a>

</div>
