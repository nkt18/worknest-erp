<?php

function appConfig($key = null, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = [];
        $envPath = dirname(__DIR__, 2) . '/.env';

        if (file_exists($envPath)) {
            $parsed = parse_ini_file($envPath);
            $config = is_array($parsed) ? $parsed : [];
        }
    }

    if ($key === null) {
        return $config;
    }

    return $config[$key] ?? $default;
}

function appBaseUrl()
{
    return rtrim((string) appConfig('BASE_URL', ''), '/');
}

function appBasePath()
{
    $path = parse_url(appBaseUrl(), PHP_URL_PATH);

    if (!$path || $path === '/') {
        return '';
    }

    return rtrim($path, '/');
}

function appPath($path = '')
{
    $normalized = '/' . ltrim($path, '/');

    return appBasePath() . $normalized;
}

function appRoute($name)
{
    static $routes = [
        'login' => '/login',
        'logout' => '/logout',
        'admin.dashboard' => '/admin/dashboard',
        'admin.employees' => '/admin/employees',
        'admin.projects' => '/admin/projects',
        'admin.tasks' => '/admin/tasks',
        'admin.activity_logs' => '/admin/activity-logs',
        'user.dashboard' => '/user/dashboard',
        'user.tasks' => '/user/tasks',
        'user.projects' => '/user/projects',
        'user.change_password' => '/user/change-password',
        'api.tasks' => '/api/tasks',
        'api.projects' => '/api/projects',
        'api.employees' => '/api/employees',
        'api.users' => '/api/users',
        'api.activity_logs' => '/api/activity-logs',
    ];

    return appPath($routes[$name] ?? '/');
}
