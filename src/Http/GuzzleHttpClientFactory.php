<?php

declare(strict_types=1);

namespace Instapago\Instapago\Http;

use Instapago\Instapago\Config\InstapagoConfig;

final readonly class GuzzleHttpClientFactory implements HttpClientFactoryInterface
{
    public function __construct(
        private ?InstapagoConfig $config = null
    ) {}

    public function create(): HttpClientInterface
    {
        return new GuzzleHttpClient($this->config);
    }

    public static function default(): self
    {
        return new self(InstapagoConfig::default());
    }

    public static function withDebug(): self
    {
        return new self(InstapagoConfig::withDebug());
    }
}
