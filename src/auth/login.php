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

    $email = trim($_POST['email'] ?? "");
    $password = trim($_POST['password'] ?? "");

    if (empty($email) || empty($password)) {
        $error = "All fields are required.";
    } else {

        $db = new Database();
        $conn = $db->connect();

        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s",$email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            if (password_verify($password,$user['password'])) {

                $_SESSION['user_id']=$user['id'];
                $_SESSION['user_name']=$user['name'];
                $_SESSION['user_role']=$user['role'];
                $_SESSION['LAST_ACTIVITY']=time();

                if ($user['role']=="admin"){
                    header("Location: /worknest-erp/src/admin/dashboard.php");
                }else{
                    header("Location: /worknest-erp/src/user/dashboard.php");
                }
                exit();

            } else {
                $error="Invalid Password";
            }

        } else {
            $error="User not found";
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>

<title>WorkNest ERP Login</title>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">


<style>

/* Professional ERP Gradient */

body{

height:100vh;
display:flex;
justify-content:center;
align-items:center;

background:linear-gradient(135deg,#1f3c88,#39a0ed);

font-family:Segoe UI;

}


/* Login Card */

.login-box{

width:420px;

background:white;

border-radius:15px;

padding:35px;

box-shadow:0 10px 30px rgba(0,0,0,0.2);

}


/* Logo */

.logo{

font-size:28px;
font-weight:700;
text-align:center;

margin-bottom:5px;

}


.subtitle{

text-align:center;
margin-bottom:20px;
color:gray;

}


/* Role Tabs */

.role-tabs{

display:flex;

justify-content:center;

margin-bottom:20px;

}


.role-tabs input{

display:none;

}


.role-tabs label{

padding:8px 20px;

border-radius:8px;

cursor:pointer;

border:1px solid #ddd;

margin:5px;

transition:0.3s;

}


.role-tabs input:checked + label{

background:#1f3c88;

color:white;

border:none;

}


/* Inputs */

.form-control{

border-radius:8px;

padding:12px;

}


/* Button */

.login-btn{

background:#1f3c88;

color:white;

font-weight:600;

border-radius:8px;

padding:12px;

}


.login-btn:hover{

background:#162d66;

}


/* Show password */

.showpass{

font-size:14px;

margin-top:5px;

}


/* Error */

.alert{

border-radius:8px;

}

</style>

</head>


<body>


<div class="login-box">


<div class="logo">

WorkNest ERP

</div>


<div class="subtitle">

Login to your account

</div>


<?php if(!empty($error)):?>

<div id="errorBox" class="alert alert-danger text-center">

<?= $error ?>

</div>

<?php endif;?>


<form method="POST">



<div class="role-tabs">


<input type="radio" name="role" id="admin" checked>

<label for="admin">
Admin Login
</label>


<input type="radio" name="role" id="user">

<label for="user">
User Login
</label>


</div>



<div class="mb-3">

<label>Email</label>

<input type="email"

name="email"

class="form-control"

required>

</div>



<div class="mb-3">

<label>Password</label>

<input type="password"

name="password"

id="password"

class="form-control"

required>


<div class="showpass">

<input type="checkbox" onclick="togglePassword()">

Show Password

</div>

</div>



<button class="btn login-btn w-100">

Login

</button>


</form>


</div>



<script>

function togglePassword(){

var x=document.getElementById("password");

if(x.type==="password"){

x.type="text";

}else{

x.type="password";

}

}


/* Auto hide error */

setTimeout(function(){

var error=document.getElementById("errorBox");

if(error){

error.style.display="none";

}

},3000);


</script>


</body>

</html>