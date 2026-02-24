<?php
require_once __DIR__ . "/../middleware/user.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if (empty($current) || empty($new) || empty($confirm)) {
        $error = "All fields are required.";
    }
    elseif ($new !== $confirm) {
        $error = "New passwords do not match.";
    }
    else {

        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (!password_verify($current, $result['password'])) {
            $error = "Current password is incorrect.";
        }
        else {

            $newHash = password_hash($new, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $newHash, $_SESSION['user_id']);
            $stmt->execute();

            $message = "Password updated successfully.";
        }
    }
}

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

<h4 class="mb-4 fw-semibold">Change Password</h4>

<div class="card shadow-sm border-0 p-4" style="max-width:500px;">

<?php if($error): ?>
<div class="alert alert-danger"><?= $error ?></div>
<?php endif; ?>

<?php if($message): ?>
<div class="alert alert-success"><?= $message ?></div>
<?php endif; ?>

<form method="POST">

<div class="mb-3">
<label class="form-label">Current Password</label>
<input type="password" name="current_password" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">New Password</label>
<input type="password" name="new_password" class="form-control" required>
</div>

<div class="mb-3">
<label class="form-label">Confirm Password</label>
<input type="password" name="confirm_password" class="form-control" required>
</div>

<button class="btn btn-primary w-100">Update Password</button>

</form>

</div>

</div>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>