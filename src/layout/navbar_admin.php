<nav class="navbar navbar-expand-lg navbar-light navbar-custom px-4">

    <div class="container-fluid">

        <span class="navbar-brand fw-bold">Admin Panel</span>

        <div class="ms-auto d-flex align-items-center gap-3">

            <i class="bi bi-bell fs-5"></i>

            <div class="dropdown">
                <a class="btn btn-light dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                    <?= $_SESSION['name'] ?? 'Admin' ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/worknest-erp/src/auth/logout.php">Logout</a></li>
                </ul>
            </div>

        </div>

    </div>

</nav>