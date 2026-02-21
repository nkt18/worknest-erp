<?php
require_once "middleware/auth.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - WorkNest ERP</title>
    <meta charset="UTF-8">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
        <span class="navbar-brand">WorkNest ERP</span>

        <div>
            <span class="text-white me-3">
                <?php echo $_SESSION['user_name']; ?> 
                (<?php echo $_SESSION['user_role']; ?>)
            </span>

            <a href="auth/logout.php" class="btn btn-danger btn-sm">
                Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h3>Welcome to Dashboard</h3>

    <?php if ($_SESSION['user_role'] === 'admin'): ?>
        <div class="alert alert-info">
            You are logged in as Admin.
        </div>
    <?php else: ?>
        <div class="alert alert-success">
            You are logged in as User.
        </div>
    <?php endif; ?>
</div>

<script>
    // Fix back button caching issue
    window.addEventListener("pageshow", function (event) {
        if (event.persisted || window.performance && window.performance.navigation.type === 2) {
            window.location.reload();
        }
    });
</script>

</body>
</html>