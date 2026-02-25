<?php
require_once __DIR__ . "/../middleware/user.php";
require_once __DIR__ . "/../config/database.php";

$db = new Database();
$conn = $db->connect();

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$current = trim($_POST['current_password']);
$new = trim($_POST['new_password']);
$confirm = trim($_POST['confirm_password']);

/* Validation */

if(empty($current) || empty($new) || empty($confirm)){

$error="All fields are required.";

}
elseif(strlen($new) < 6){

$error="Password must be at least 6 characters.";

}
elseif($new !== $confirm){

$error="Passwords do not match.";

}
else{

$stmt=$conn->prepare("
SELECT password
FROM users
WHERE id=?
");

$stmt->bind_param("i",$_SESSION['user_id']);
$stmt->execute();

$user=$stmt->get_result()->fetch_assoc();


if(!password_verify($current,$user['password'])){

$error="Current password incorrect.";

}
else{

$newHash=password_hash($new,PASSWORD_DEFAULT);

$stmt=$conn->prepare("
UPDATE users
SET password=?
WHERE id=?
");

$stmt->bind_param(
"si",
$newHash,
$_SESSION['user_id']
);

$stmt->execute();

/* ACTIVITY LOG */

$action="Changed password";

$log=$conn->prepare("
INSERT INTO activity_logs(user_id,action)
VALUES(?,?)
");

$log->bind_param(
"is",
$_SESSION['user_id'],
$action
);

$log->execute();


$message="Password updated successfully.";

}

}

}

require_once __DIR__ . "/../layout/user_header.php";
require_once __DIR__ . "/../layout/user_sidebar.php";
require_once __DIR__ . "/../layout/user_navbar.php";
?>

<div class="content">

<div class="mb-4">

<h4 class="fw-semibold">
Change Password
</h4>

<small class="text-muted">
Update your account password
</small>

</div>

<div class="card shadow-sm border-0 p-4"
style="max-width:500px;">


<?php if($error): ?>

<div class="alert alert-danger"
id="alertBox">

<?= $error ?>

</div>

<?php endif; ?>


<?php if($message): ?>

<div class="alert alert-success"
id="alertBox">

<?= $message ?>

</div>

<?php endif; ?>


<form method="POST">

<div class="mb-3">

<label class="form-label">
Current Password
</label>

<input type="password"
name="current_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
New Password
</label>

<input type="password"
name="new_password"
class="form-control"
required>

</div>

<div class="mb-3">

<label class="form-label">
Confirm Password
</label>

<input type="password"
name="confirm_password"
class="form-control"
required>

</div>

<button class="btn btn-primary w-100">

Update Password

</button>

</form>

</div>

</div>

<script>

/* Auto hide alerts */

setTimeout(function(){

let alert=document.getElementById("alertBox");

if(alert){

alert.remove();

}

},3000);

</script>

<?php require_once __DIR__ . "/../layout/footer.php"; ?>