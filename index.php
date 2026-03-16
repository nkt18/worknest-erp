<?php

require_once __DIR__ . '/src/helpers/app.php';

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestPath = parse_url($requestUri, PHP_URL_PATH) ?: '/';
$basePath = appBasePath();

if ($basePath !== '' && str_starts_with($requestPath, $basePath)) {
    $requestPath = substr($requestPath, strlen($basePath)) ?: '/';
}

$normalizedPath = '/' . trim($requestPath, '/');

if ($normalizedPath === '//') {
    $normalizedPath = '/';
}

$routes = [
    '/' => __DIR__ . '/src/auth/login.php',
    '/login' => __DIR__ . '/src/auth/login.php',
    '/logout' => __DIR__ . '/src/auth/logout.php',
    '/admin/dashboard' => __DIR__ . '/src/admin/dashboard.php',
    '/admin/employees' => __DIR__ . '/src/admin/modules/employees/index.php',
    '/admin/projects' => __DIR__ . '/src/admin/modules/projects/index.php',
    '/admin/tasks' => __DIR__ . '/src/admin/modules/tasks/index.php',
    '/admin/activity-logs' => __DIR__ . '/src/admin/modules/activity_logs/index.php',
    '/user/dashboard' => __DIR__ . '/src/user/dashboard.php',
    '/user/tasks' => __DIR__ . '/src/user/tasks.php',
    '/user/projects' => __DIR__ . '/src/user/projects.php',
    '/user/change-password' => __DIR__ . '/src/user/change_password.php',
    '/api/tasks' => __DIR__ . '/src/api/tasks.php',
    '/api/projects' => __DIR__ . '/src/api/projects.php',
    '/api/employees' => __DIR__ . '/src/api/employees.php',
    '/api/users' => __DIR__ . '/src/api/users.php',
    '/api/activity-logs' => __DIR__ . '/src/api/activity_logs.php',
];

if (!isset($routes[$normalizedPath])) {
    http_response_code(404);
    echo 'Not Found';
    exit;
}

require $routes[$normalizedPath];
