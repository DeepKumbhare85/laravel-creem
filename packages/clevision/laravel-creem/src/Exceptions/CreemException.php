<?php

namespace Clevision\Creem\Exceptions;

use Exception;

class CreemException extends Exception
{
    protected array $errors;

    public function __construct(string $message, int $code = 0, array $errors = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public static function fromResponse(int $status, array $body): static
    {
        $message = $body['message'] ?? $body['error'] ?? "Creem API error (HTTP {$status})";
        $errors  = $body['errors'] ?? [];

        return new static($message, $status, $errors);
    }
}
