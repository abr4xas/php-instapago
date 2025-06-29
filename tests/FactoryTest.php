<?php

declare(strict_types=1);

use Instapago\Instapago\Http\GuzzleHttpClient;
use Instapago\Instapago\Http\GuzzleHttpClientFactory;

describe('Factory Tests', function () {
    it('can create HTTP client with factory', function () {
        $factory = GuzzleHttpClientFactory::default();
        $client = $factory->create();

        expect($client)->toBeInstanceOf(GuzzleHttpClient::class);
    });

    it('can create HTTP client with debug', function () {
        $factory = GuzzleHttpClientFactory::withDebug();
        $client = $factory->create();

        expect($client)->toBeInstanceOf(GuzzleHttpClient::class);
    });
});
