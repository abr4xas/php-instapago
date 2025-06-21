<?php

declare(strict_types=1);

use Instapago\Instapago\Services\ResponseHandler;

describe('Services Tests', function () {
    it('can handle successful response', function () {
        $handler = new ResponseHandler();
        $response = [
            'code' => '201',
            'message' => 'Pago Aprobado',
            'voucher' => 'Test voucher',
            'id' => 'payment-123',
            'reference' => 'ref-456',
        ];

        $result = $handler->handleResponse($response);

        expect($result)->toBeArray()
            ->and($result['code'])->toBe('201')
            ->and($result['message'])->toBe('Pago Aprobado')
            ->and($result['id_pago'])->toBe('payment-123');
    });

    it('throws exception for error response', function () {
        $handler = new ResponseHandler();
        $response = ['code' => '400'];

        expect(fn () => $handler->handleResponse($response))
            ->toThrow(Instapago\Instapago\Exceptions\InstapagoInvalidInputException::class);
    });
});
