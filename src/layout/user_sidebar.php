<?php $current = $_SERVER['REQUEST_URI']; ?>

<div class="sidebar">

    <h5 class="text-center mb-4">WorkNest ERP</h5>

    <a href="/worknest-erp/src/user/dashboard.php"
       class="<?= strpos($current,'dashboard')!==false?'active':'' ?>">
       <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="/worknest-erp/src/user/tasks.php"
       class="<?= strpos($current,'tasks')!==false?'active':'' ?>">
       <i class="bi bi-list-task me-2"></i> My Tasks
    </a>

</div>