<?php

declare(strict_types=1);

namespace Instapago\Instapago\Logging;

use Throwable;

final class NullLogger implements LoggerInterface
{
    public function logRequest(string $method, string $url, array $data): void
    {
        // No hace nada - Null Object Pattern
    }

    public function logResponse(array $response): void
    {
        // No hace nada - Null Object Pattern
    }

    public function logError(Throwable $exception): void
    {
        // No hace nada - Null Object Pattern
    }

    public function info(string $message, array $context = []): void
    {
        // No hace nada - Null Object Pattern
    }

    public function warning(string $message, array $context = []): void
    {
        // No hace nada - Null Object Pattern
    }

    public function error(string $message, array $context = []): void
    {
        // No hace nada - Null Object Pattern
    }
}
