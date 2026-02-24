<?php
require_once dirname(__DIR__, 3) . "/middleware/admin.php";
require_once dirname(__DIR__, 3) . "/config/database.php";
$db = new Database();
$conn = $db->connect();

if (isset($_GET['id'])) {

    $employee_id = intval($_GET['id']);

    // First get user_id from employees table
    $stmt = $conn->prepare("SELECT user_id FROM employees WHERE id = ?");
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $stmt->bind_result($user_id);
    $stmt->fetch();
    $stmt->close();

    if ($user_id) {

        // Delete from employees
        $stmt2 = $conn->prepare("DELETE FROM employees WHERE id = ?");
        $stmt2->bind_param("i", $employee_id);
        $stmt2->execute();
        $stmt2->close();

        // Delete from users
        $stmt3 = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $stmt3->close();
    }

    header("Location: index.php?deleted=1");
    exit();
}