<?php

declare(strict_types=1);

namespace Instapago\Instapago\Config;

final class InstapagoConfig
{
    public function __construct(
        public string $baseUri = 'https://api.instapago.com/',
        public int $timeout = 30,
        public bool $debug = false,
        public array $headers = []
    ) {}

    public function getGuzzleConfig(): array
    {
        $config = [
            'base_uri' => $this->baseUri,
            'timeout' => $this->timeout,
        ];

        if (! empty($this->headers)) {
            $config['headers'] = $this->headers;
        }

        if ($this->debug) {
            $config['debug'] = true;
        }

        return $config;
    }

    public static function default(): self
    {
        return new self();
    }

    public static function withDebug(): self
    {
        return new self(
            debug: true
        );
    }
}
