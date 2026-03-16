<?php

require_once __DIR__ . '/errors.php';

function rateLimitStorageDir()
{
    $directory = sys_get_temp_dir() . '/worknest-erp-rate-limits';

    if (!is_dir($directory)) {
        mkdir($directory, 0775, true);
    }

    return $directory;
}

function rateLimitFilePath($key)
{
    return rateLimitStorageDir() . '/' . hash('sha256', $key) . '.json';
}

function rateLimitRead($key)
{
    $path = rateLimitFilePath($key);

    if (!file_exists($path)) {
        return [
            'attempts' => [],
            'lock_until' => 0,
        ];
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    if (!is_array($decoded)) {
        return [
            'attempts' => [],
            'lock_until' => 0,
        ];
    }

    return [
        'attempts' => array_values(array_filter($decoded['attempts'] ?? [], 'is_int')),
        'lock_until' => (int) ($decoded['lock_until'] ?? 0),
    ];
}

function rateLimitWrite($key, $state)
{
    file_put_contents(rateLimitFilePath($key), json_encode($state, JSON_PRETTY_PRINT), LOCK_EX);
}

function rateLimitForget($key)
{
    $path = rateLimitFilePath($key);

    if (file_exists($path)) {
        unlink($path);
    }
}

function rateLimitPruneAttempts($attempts, $windowSeconds, $now)
{
    return array_values(array_filter($attempts, static function ($attemptAt) use ($windowSeconds, $now) {
        return ($now - (int) $attemptAt) < $windowSeconds;
    }));
}

function rateLimitStatus($key, $maxAttempts, $windowSeconds, $lockoutSeconds)
{
    $now = time();
    $state = rateLimitRead($key);
    $state['attempts'] = rateLimitPruneAttempts($state['attempts'], $windowSeconds, $now);

    if (($state['lock_until'] ?? 0) > $now) {
        return [
            'blocked' => true,
            'retry_after' => (int) $state['lock_until'] - $now,
        ];
    }

    if (($state['lock_until'] ?? 0) > 0 && $state['lock_until'] <= $now) {
        $state['lock_until'] = 0;
        rateLimitWrite($key, $state);
    }

    if (count($state['attempts']) >= $maxAttempts) {
        $state['lock_until'] = $now + $lockoutSeconds;
        rateLimitWrite($key, $state);

        return [
            'blocked' => true,
            'retry_after' => $lockoutSeconds,
        ];
    }

    return [
        'blocked' => false,
        'retry_after' => 0,
    ];
}

function rateLimitHit($key, $maxAttempts, $windowSeconds, $lockoutSeconds)
{
    $now = time();
    $state = rateLimitRead($key);
    $state['attempts'] = rateLimitPruneAttempts($state['attempts'], $windowSeconds, $now);
    $state['attempts'][] = $now;

    if (count($state['attempts']) >= $maxAttempts) {
        $state['lock_until'] = $now + $lockoutSeconds;
    }

    rateLimitWrite($key, $state);
}

function loginRateLimitSubjects($email, $ipAddress)
{
    $normalizedEmail = strtolower(trim((string) $email));
    $normalizedIp = trim((string) $ipAddress) ?: 'unknown';

    return [
        [
            'key' => 'login:email:' . $normalizedEmail,
            'max_attempts' => 5,
            'window_seconds' => 900,
            'lockout_seconds' => 900,
        ],
        [
            'key' => 'login:ip:' . $normalizedIp,
            'max_attempts' => 20,
            'window_seconds' => 900,
            'lockout_seconds' => 900,
        ],
    ];
}

function currentRequestIp()
{
    return (string) ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
}

function loginRateLimitGuard($email, $ipAddress = null)
{
    $subjects = loginRateLimitSubjects($email, $ipAddress ?? currentRequestIp());
    $retryAfter = 0;

    foreach ($subjects as $subject) {
        $status = rateLimitStatus(
            $subject['key'],
            $subject['max_attempts'],
            $subject['window_seconds'],
            $subject['lockout_seconds']
        );

        if ($status['blocked']) {
            $retryAfter = max($retryAfter, (int) $status['retry_after']);
        }
    }

    if ($retryAfter > 0) {
        throw new RateLimitException('Too many login attempts. Try again later.', $retryAfter);
    }
}

function loginRateLimitFailure($email, $ipAddress = null)
{
    foreach (loginRateLimitSubjects($email, $ipAddress ?? currentRequestIp()) as $subject) {
        rateLimitHit(
            $subject['key'],
            $subject['max_attempts'],
            $subject['window_seconds'],
            $subject['lockout_seconds']
        );
    }
}

function loginRateLimitClear($email, $ipAddress = null)
{
    foreach (loginRateLimitSubjects($email, $ipAddress ?? currentRequestIp()) as $subject) {
        rateLimitForget($subject['key']);
    }
}

function formatRetryAfter($seconds)
{
    $seconds = max(0, (int) $seconds);
    $minutes = intdiv($seconds + 59, 60);

    if ($minutes <= 1) {
        return '1 minute';
    }

    return $minutes . ' minutes';
}
