<?php
require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

    // Start transaction
    $conn->begin_transaction();

    try {

        // Check duplicate email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            throw new Exception("Email already exists.");
        }

        // Default password
        $defaultPassword = password_hash("123456", PASSWORD_DEFAULT);

        // Insert into users
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $name, $email, $defaultPassword);
        $stmt->execute();

        $user_id = $stmt->insert_id;

        // Insert into employees
        $stmt2 = $conn->prepare("INSERT INTO employees (user_id, designation, phone, department) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("isss", $user_id, $designation, $phone, $department);
        $stmt2->execute();

        // Commit if everything successful
        $conn->commit();

        header("Location: index.php?added=1");
        exit();

    } catch (Exception $e) {

        // Rollback if any error
        $conn->rollback();
        die($e->getMessage());
    }
}