<?php

require_once __DIR__ . '/../helpers/app.php';
require_once __DIR__ . '/../helpers/api.php';
require_once __DIR__ . '/../helpers/csrf.php';
require_once __DIR__ . '/../helpers/errors.php';
require_once __DIR__ . '/../helpers/session.php';

enforceSessionSecurity();

// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if user logged in
if (!isset($_SESSION['user_id'])) {
    if (apiIsRequest()) {
        apiResponse(false, "Authentication required", null, 401);
    }

    header("Location: " . appRoute('login'));
    exit();
}

requireCsrfToken();

function currentUserId()
{
    return (int) ($_SESSION['user_id'] ?? 0);
}

function currentUserRole()
{
    return $_SESSION['user_role'] ?? null;
}

function requireRole($role)
{
    if (currentUserRole() !== $role) {
        if (apiIsRequest()) {
            apiResponse(false, "Unauthorized", null, 403);
        }

        http_response_code(403);
        echo "Forbidden";
        exit();
    }
}
