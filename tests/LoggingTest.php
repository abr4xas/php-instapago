<?php

declare(strict_types=1);

use Instapago\Instapago\Logging\NullLogger;

describe('Logging Tests', function () {
    it('can use NullLogger without errors', function () {
        $logger = new NullLogger();

        expect(fn () => $logger->info('test'))->not->toThrow(Exception::class)
            ->and(fn () => $logger->error('test'))->not->toThrow(Exception::class)
            ->and(fn () => $logger->logRequest('GET', '/test', []))->not->toThrow(Exception::class);
    });
});
