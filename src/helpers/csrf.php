<?php

require_once __DIR__ . '/session.php';

function csrfToken()
{
    startSecureSession();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrfInput()
{
    return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function csrfRequestToken()
{
    $headerToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

    if (is_string($headerToken) && $headerToken !== '') {
        return $headerToken;
    }

    return (string) ($_POST['_csrf'] ?? '');
}

function isStateChangingRequest()
{
    return in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['POST', 'PUT', 'PATCH', 'DELETE'], true);
}

function verifyCsrfToken()
{
    $requestToken = csrfRequestToken();
    $sessionToken = csrfToken();

    return $requestToken !== '' && hash_equals($sessionToken, $requestToken);
}

function requireCsrfToken()
{
    if (!isStateChangingRequest()) {
        return;
    }

    if (!verifyCsrfToken()) {
        if (function_exists('apiIsRequest') && apiIsRequest()) {
            apiResponse(false, 'Invalid CSRF token', null, 419);
        }

        http_response_code(419);
        exit('Invalid CSRF token');
    }
}
