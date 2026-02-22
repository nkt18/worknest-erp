<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $designation = trim($_POST['designation']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);

    // Auto generate default password
    $defaultPassword = password_hash("123456", PASSWORD_DEFAULT);

    if (empty($name) || empty($email) || empty($designation) || empty($phone) || empty($department)) {
        die("All fields are required.");
    }

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        die("Email already exists.");
    }

    // Insert into users
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->bind_param("sss", $name, $email, $defaultPassword);
    $stmt->execute();

    $user_id = $stmt->insert_id;

    // Insert into employees
    $stmt2 = $conn->prepare("INSERT INTO employees (user_id, designation, phone, department) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param("isss", $user_id, $designation, $phone, $department);
    $stmt2->execute();

    header("Location: index.php");
    exit();
}