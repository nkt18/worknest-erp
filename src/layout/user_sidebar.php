<?php
require_once __DIR__ . "/../helpers/app.php";
$current = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar">

    <h5 class="text-center mb-4">WorkNest ERP</h5>

    <a href="<?= htmlspecialchars(appRoute('user.dashboard')) ?>"
       class="<?= strpos($current,'dashboard')!==false?'active':'' ?>">
       <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="<?= htmlspecialchars(appRoute('user.tasks')) ?>"
       class="<?= strpos($current,'tasks')!==false?'active':'' ?>">
       <i class="bi bi-list-task me-2"></i> My Tasks
    </a>

</div>
