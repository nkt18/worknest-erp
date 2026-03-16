<?php require_once __DIR__ . '/../helpers/csrf.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>WorkNest ERP</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= htmlspecialchars(csrfToken()) ?>">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        window.apiFetch = (url, options = {}) => {
            const config = { ...options };
            const method = (config.method || 'GET').toUpperCase();
            const headers = new Headers(config.headers || {});

            if (!headers.has('X-Requested-With')) {
                headers.set('X-Requested-With', 'XMLHttpRequest');
            }

            if (['POST', 'PUT', 'PATCH', 'DELETE'].includes(method)) {
                headers.set('X-CSRF-Token', window.csrfToken);
            }

            config.headers = headers;
            config.credentials = config.credentials || 'same-origin';

            return fetch(url, config);
        };
    </script>

    <style>
        body {
    background-color: #eef1f6;
    font-family: 'Segoe UI', sans-serif;
}

.sidebar {
    width: 250px;
    height: 100vh;
    position: fixed;
    background: #111827;
    color: white;
}

.sidebar h4 {
    font-weight: 600;
    letter-spacing: 0.5px;
}

.sidebar a {
    color: #9ca3af;
    text-decoration: none;
    padding: 14px 22px;
    display: block;
    font-size: 15px;
    transition: all 0.3s ease;
}

.sidebar a:hover {
    background-color: #1f2937;
    color: white;
}

.sidebar .active {
    background: linear-gradient(90deg, #2563eb, #1d4ed8);
    color: white;
}

.content {
    margin-left: 250px;
    padding: 30px;
}

.navbar-custom {
    margin-left: 250px;
    background: white;
    border-bottom: 1px solid #e5e7eb;
}

.dashboard-card {
    border: none;
    border-radius: 14px;
    background: white;
    box-shadow: 0 6px 18px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 22px rgba(0,0,0,0.08);
}

.stat-number {
    font-size: 30px;
    font-weight: 600;
}

.card-icon {
    font-size: 22px;
    opacity: 0.6;
}
    </style>
</head>
<body>
