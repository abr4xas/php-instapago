<?php

declare(strict_types=1);

namespace Instapago\Instapago\Http;

interface HttpClientInterface
{
    public function request(string $method, string $url, array $data = []): array;
}
