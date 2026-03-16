<?php

class ValidationException extends InvalidArgumentException
{
}

class NotFoundException extends RuntimeException
{
}

class AuthorizationException extends RuntimeException
{
}

class RateLimitException extends RuntimeException
{
    private $retryAfterSeconds;

    public function __construct($message, $retryAfterSeconds = 0)
    {
        parent::__construct($message);
        $this->retryAfterSeconds = max(0, (int) $retryAfterSeconds);
    }

    public function retryAfterSeconds()
    {
        return $this->retryAfterSeconds;
    }
}
