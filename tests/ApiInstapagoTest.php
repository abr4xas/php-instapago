<?php

declare(strict_types=1);

use Instapago\Instapago\Api;
use Instapago\Instapago\Exceptions\InstapagoException;
use Instapago\Instapago\Exceptions\ValidationException;

beforeEach(function () {
    $this->api = new Api('1E488391-7934-4301-9F8E-17DC99AB49B3', '691f77db9d62c0f2fe191ce69ed9bb41');

    $this->dataOk = [
        'amount' => '200',
        'description' => 'Test payment',
        'card_holder' => 'juan peñalver',
        'card_holder_id' => '11111111',
        'card_number' => '4111111111111111',
        'cvc' => '123',
        'expiration' => '12/2026',
        'ip' => '127.0.0.1',
    ];

    $this->dataNoOk = [
        'amount' => '200.00',
        'description' => 'PHPUnit Test Payment',
        'card_holder' => 'juan peñalver',
        'card_holder_id' => '11111111',
        'card_number' => '4111111111111112',
        'cvc' => '123',
        'expiration' => '12/2026',
        'ip' => '127.0.0.1',
    ];
});

it('can create a direct payment', function () {
    $payment = $this->api->directPayment($this->dataOk);

    expect($payment['code'])->toBeString()
        ->and($payment['code'])->toBe('201')
        ->and($payment['message'])->toBe('Pago Aprobado')
        ->and($payment['id_pago'])->toBeString();

    return $payment;
});

it('can create a reserved payment', function () {

    $payment = $this->api->reservePayment($this->dataOk);

    expect($payment['code'])->toBeString()
        ->and($payment['code'])->toBe('201');

    return $payment;
});

it('can complete the payment', function ($param) {
    $payment = $this->api->completePayment([
        'id' => $param['id_pago'],
        'amount' => '200',
    ]);

    expect($payment['message'])->toBe('Pago Completado');

    return $param['id_pago'];
})->depends('it can create a reserved payment');

it('can check if the payment is authorized', function ($payment) {
    $payment = $this->api->query($payment);

    expect($payment['message'])->toBe('Completada');
})->depends('it can complete the payment');

it('can cancel a payment', function ($param) {
    $payment = $this->api->cancel($param['id_pago']);

    expect($payment['message'])->toBe('El pago ha sido anulado');
})->depends('it can create a direct payment');

it('throws an exception if keys are missing', function () {
    new Api('', 'publicKey');
})->throws(InstapagoException::class);

it('throws an exception if public key is missing', function () {
    new Api('key', '');
})->throws(InstapagoException::class);

it('throws validation exception for missing fields in direct payment', function () {
    expect(fn () => $this->api->directPayment([]))
        ->toThrow(ValidationException::class);
});
