<?php

require_once __DIR__ . '/app.php';

function sessionCookiePath()
{
    return appBasePath() ?: '/';
}

function sessionIsSecureRequest()
{
    return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
}

function startSecureSession()
{
    if (session_status() !== PHP_SESSION_NONE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', sessionIsSecureRequest() ? '1' : '0');

    session_name('WORKNESTSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => sessionCookiePath(),
        'domain' => '',
        'secure' => sessionIsSecureRequest(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    session_start();
}

function sessionFingerprint()
{
    return hash('sha256', (string) ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
}

function destroyAuthenticatedSession()
{
    if (session_status() === PHP_SESSION_NONE) {
        startSecureSession();
    }

    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'] ?? '/',
            'domain' => $params['domain'] ?? '',
            'secure' => !empty($params['secure']),
            'httponly' => !empty($params['httponly']),
            'samesite' => $params['samesite'] ?? 'Lax',
        ]);
    }

    session_destroy();
}

function loginUserSession($user)
{
    startSecureSession();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['LAST_ACTIVITY'] = time();
    $_SESSION['SESSION_CREATED_AT'] = time();
    $_SESSION['LAST_REGENERATED_AT'] = time();
    $_SESSION['SESSION_FINGERPRINT'] = sessionFingerprint();
}

function enforceSessionSecurity()
{
    startSecureSession();

    if (!isset($_SESSION['user_id'])) {
        return;
    }

    $now = time();
    $timeout = 1800;
    $rotationInterval = 300;

    if (($_SESSION['SESSION_FINGERPRINT'] ?? null) !== sessionFingerprint()) {
        destroyAuthenticatedSession();
        header('Location: ' . appRoute('login') . '?reauth=1');
        exit();
    }

    if (isset($_SESSION['LAST_ACTIVITY']) && ($now - (int) $_SESSION['LAST_ACTIVITY']) > $timeout) {
        destroyAuthenticatedSession();
        header('Location: ' . appRoute('login') . '?timeout=1');
        exit();
    }

    if (!isset($_SESSION['LAST_REGENERATED_AT']) || ($now - (int) $_SESSION['LAST_REGENERATED_AT']) > $rotationInterval) {
        session_regenerate_id(true);
        $_SESSION['LAST_REGENERATED_AT'] = $now;
    }

    $_SESSION['LAST_ACTIVITY'] = $now;
}
