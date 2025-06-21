<?php

declare(strict_types=1);

use Instapago\Instapago\Enums\PaymentType;

describe('Enums Tests', function () {
    it('has correct PaymentType values', function () {
        expect(PaymentType::DIRECT->value)->toBe('2')
            ->and(PaymentType::RESERVED->value)->toBe('1');
    });
});
