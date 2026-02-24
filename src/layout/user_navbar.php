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
            <a class="dropdown-item" href="/worknest-erp/src/user/profile.php">
                <i class="bi bi-person me-2"></i> Profile
            </a>
        </li>

        <li>
            <a class="dropdown-item" href="/worknest-erp/src/user/change_password.php">
                <i class="bi bi-shield-lock me-2"></i> Change Password
            </a>
        </li>

        <li><hr class="dropdown-divider"></li>

        <li>
            <a class="dropdown-item text-danger"
               href="/worknest-erp/src/auth/logout.php">
               <i class="bi bi-box-arrow-right me-2"></i> Logout
            </a>
        </li>
    </ul>
</div>

</nav>