<?php
session_start();
require_once "../config/database.php";

$error = "";

if (isset($_SESSION['user_id'])) {

    if ($_SESSION['user_role'] === 'admin') {
        header("Location: /worknest-erp/src/admin/dashboard.php");
    } else {
        header("Location: /worknest-erp/src/user/dashboard.php");
    }
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = isset($_POST['email']) ? trim($_POST['email']) : "";
    $password = isset($_POST['password']) ? trim($_POST['password']) : "";

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['LAST_ACTIVITY'] = time();

                if ($user['role'] === 'admin') {
                    header("Location: /worknest-erp/src/admin/dashboard.php");
                } else {
                    header("Location: /worknest-erp/src/user/dashboard.php");
                }
                exit();

            } else {
                $error = "Invalid password.";
            }

        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - WorkNest ERP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center">
<div class="col-md-4">

<div class="card shadow border-0">
<div class="card-header text-center bg-white">
<h4 class="fw-semibold">WorkNest ERP</h4>
<small class="text-muted">Login to your account</small>
</div>

<div class="card-body">

<?php if (!empty($error)) : ?>
<div class="alert alert-danger">
<?= $error ?>
</div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<button type="submit" class="btn btn-dark w-100">
Login
</button>

</form>

</div>
</div>

</div>
</div>
</div>

</body>
</html>