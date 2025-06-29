<?php

declare(strict_types=1);

use Instapago\Instapago\Api;
use Instapago\Instapago\Config\InstapagoConfig;
use Instapago\Instapago\Http\GuzzleHttpClient;
use Instapago\Instapago\Logging\NullLogger;

describe('Integration Tests', function () {
    it('can create Api with custom dependencies', function () {
        $config = InstapagoConfig::withDebug();
        $httpClient = new GuzzleHttpClient($config);
        $logger = new NullLogger();

        $api = new Api('test-key', 'test-public-key', $httpClient, $logger);

        expect($api)->toBeInstanceOf(Api::class);
    });

    it('implements InstapagoApiInterface', function () {
        $api = new Api('test-key', 'test-public-key');

        expect($api)->toBeInstanceOf(Instapago\Instapago\InstapagoApiInterface::class);
    });
});
