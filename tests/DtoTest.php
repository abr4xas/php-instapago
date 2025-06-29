<?php

declare(strict_types=1);

use Instapago\Instapago\DTOs\CompletePaymentRequest;
use Instapago\Instapago\DTOs\PaymentRequest;
use Instapago\Instapago\DTOs\PaymentResponse;

describe('DTOs Tests', function () {
    it('can create PaymentRequest from array', function () {

        $request = PaymentRequest::fromArray([
            'amount' => '200',
            'description' => 'Test payment',
            'card_holder' => 'juan peñalver',
            'card_holder_id' => '11111111',
            'card_number' => '4111111111111111',
            'cvc' => '123',
            'expiration' => '12/2026',
            'ip' => '127.0.0.1',
        ]);

        expect($request->amount)->toBe(200.0)
            ->and($request->description)->toBe('Test payment')
            ->and($request->cardHolder)->toBe('juan peñalver')
            ->and($request->cardHolderId)->toBe('11111111')
            ->and($request->cardNumber)->toBe('4111111111111111')
            ->and($request->cvc)->toBe('123')
            ->and($request->expiration)->toBe('12/2026')
            ->and($request->ip)->toBe('127.0.0.1');
    });

    it('can convert PaymentRequest to array', function () {
        $request = new PaymentRequest(
            amount: 200.50,
            description: 'Test payment',
            cardHolder: 'Juan Perez',
            cardHolderId: '12345678',
            cardNumber: '4111111111111111',
            cvc: '123',
            expiration: '12/2026',
            ip: '127.0.0.1'
        );

        $array = $request->toArray();

        expect($array)->toBe([
            'amount' => 200.50,
            'description' => 'Test payment',
            'card_holder' => 'Juan Perez',
            'card_holder_id' => '12345678',
            'card_number' => '4111111111111111',
            'cvc' => '123',
            'expiration' => '12/2026',
            'ip' => '127.0.0.1',
        ]);
    });

    it('can create PaymentResponse from array', function () {
        $data = [
            'code' => '201',
            'message' => 'Pago Aprobado',
            'voucher' => 'Test voucher',
            'id' => 'payment-123',
            'reference' => 'ref-456',
        ];

        $response = PaymentResponse::fromArray($data);

        expect($response->code)->toBe('201')
            ->and($response->message)->toBe('Pago Aprobado')
            ->and($response->voucher)->toBe('Test voucher')
            ->and($response->idPago)->toBe('payment-123')
            ->and($response->reference)->toBe('ref-456')
            ->and($response->isSuccessful())->toBeTrue();
    });

    it('can create CompletePaymentRequest', function () {
        $request = CompletePaymentRequest::fromArray([
            'id' => 'payment-123',
            'amount' => 200.50,
        ]);

        expect($request->id)->toBe('payment-123')
            ->and($request->amount)->toBe(200.50);
    });
});
