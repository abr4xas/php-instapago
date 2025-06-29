<?php

declare(strict_types=1);

namespace Instapago\Instapago\Logging;

use Throwable;

interface LoggerInterface
{
    public function logRequest(string $method, string $url, array $data): void;

    public function logResponse(array $response): void;

    public function logError(Throwable $exception): void;

    public function info(string $message, array $context = []): void;

    public function warning(string $message, array $context = []): void;

    public function error(string $message, array $context = []): void;
}
