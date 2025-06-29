<?php

declare(strict_types=1);

use Instapago\Instapago\Config\InstapagoConfig;

describe('Configuration Tests', function () {
    it('can create default config', function () {
        $config = InstapagoConfig::default();

        expect($config->baseUri)->toBe('https://api.instapago.com/')
            ->and($config->timeout)->toBe(30)
            ->and($config->debug)->toBeFalse();
    });

    it('can create config with debug', function () {
        $config = InstapagoConfig::withDebug();

        expect($config->baseUri)->toBe('https://api.instapago.com/')
            ->and($config->timeout)->toBe(30)
            ->and($config->debug)->toBeTrue();
    });

    it('can create custom config', function () {
        $config = new InstapagoConfig(
            baseUri: 'https://custom.api.com/',
            timeout: 60,
            debug: true,
            headers: ['X-Custom' => 'test']
        );

        expect($config->baseUri)->toBe('https://custom.api.com/')
            ->and($config->timeout)->toBe(60)
            ->and($config->debug)->toBeTrue()
            ->and($config->headers)->toBe(['X-Custom' => 'test']);
    });
});
