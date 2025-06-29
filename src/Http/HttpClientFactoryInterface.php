<?php

declare(strict_types=1);

namespace Instapago\Instapago\Http;

interface HttpClientFactoryInterface
{
    public function create(): HttpClientInterface;
}
