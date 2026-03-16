<?php

require_once __DIR__ . '/app.php';
require_once __DIR__ . '/errors.php';

function apiResponse($success, $message = "", $data = null, $code = 200)
{
    http_response_code($code);
    header("Content-Type: application/json");

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data,
    ]);

    exit;
}

function apiReadJson()
{
    $raw = file_get_contents("php://input");
    $data = json_decode($raw, true);

    return is_array($data) ? $data : [];
}

function apiIsRequest()
{
    return str_starts_with($_SERVER['REQUEST_URI'] ?? '', appPath('/api/'));
}

function apiRequireId()
{
    parse_str($_SERVER['QUERY_STRING'] ?? "", $params);
    $id = (int) ($params['id'] ?? 0);

    if ($id <= 0) {
        apiResponse(false, "Invalid ID", null, 422);
    }

    return $id;
}

function apiHandleException($exception)
{
    if ($exception instanceof ValidationException) {
        apiResponse(false, $exception->getMessage(), null, 422);
    }

    if ($exception instanceof AuthorizationException) {
        apiResponse(false, $exception->getMessage(), null, 403);
    }

    if ($exception instanceof NotFoundException) {
        apiResponse(false, $exception->getMessage(), null, 404);
    }

    if ($exception instanceof RateLimitException) {
        header('Retry-After: ' . $exception->retryAfterSeconds());
        apiResponse(false, $exception->getMessage(), null, 429);
    }

    apiResponse(false, "Internal server error", null, 500);
}
