<?php require_once __DIR__ . "/../helpers/app.php"; ?>
<nav class="navbar navbar-user px-4 py-2 d-flex justify-content-between align-items-center">

<div>
    <strong>User Dashboard</strong>
</div>

<div class="dropdown">
    <button class="btn btn-light dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">
        <i class="bi bi-person-circle"></i>
        <?= htmlspecialchars($_SESSION['user_name']) ?>
    </button>

    <ul class="dropdown-menu dropdown-menu-end shadow">
        <li>
            <a class="dropdown-item" href="#">
                <i class="bi bi-person me-2"></i> Profile
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="<?= htmlspecialchars(appRoute('user.change_password')) ?>">
                <i class="bi bi-shield-lock me-2"></i> Change Password
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <form method="POST" action="<?= htmlspecialchars(appRoute('logout')) ?>" class="m-0">
                <?= csrfInput() ?>
                <button type="submit" class="dropdown-item text-danger">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>
</div>

</nav>
