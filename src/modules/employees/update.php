<?php
require_once "../../middleware/admin.php";
require_once "../../config/database.php";

$db = new Database();
$conn = $db->connect();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $employee_id = intval($_POST['employee_id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $designation = trim($_POST['designation']);
    $phone = trim($_POST['phone']);
    $department = trim($_POST['department']);

    if (empty($name) || empty($email) || empty($designation) || empty($phone) || empty($department)) {
        die("All fields are required.");
    }

    // Update users table
    $stmt = $conn->prepare("UPDATE users 
                            JOIN employees ON users.id = employees.user_id
                            SET users.name = ?, users.email = ?
                            WHERE employees.id = ?");
    $stmt->bind_param("ssi", $name, $email, $employee_id);
    $stmt->execute();

    // Update employees table
    $stmt2 = $conn->prepare("UPDATE employees 
                             SET designation = ?, phone = ?, department = ?
                             WHERE id = ?");
    $stmt2->bind_param("sssi", $designation, $phone, $department, $employee_id);
    $stmt2->execute();

    header("Location: index.php?updated=1");
    exit();
}