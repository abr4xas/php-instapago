<?php

use Instapago\Instapago\Api;

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    $this->api = new Api('1E488391-7934-4301-9F8E-17DC99AB49B3', '691f77db9d62c0f2fe191ce69ed9bb41');

    $this->dataOk = [
        'amount' => '200',
        'description' => 'PHPUnit Test Payment',
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

it('can trow an invalid input error', function () {
    $payment = $this->api->directPayment($this->dataNoOk);
    expect($payment)->toBe('Error al validar los datos enviados');
});

it('can create a direct payment', function () {
    $payment = $this->api->directPayment($this->dataOk);

    assertEquals(201, $payment['code']);
    expect($payment['message'])->toBe('Pago Aprobado')
        ->and($payment['id_pago'])->toBeString();

    return $payment;
});

it('can create a reserved payment', function () {
    $payment = $this->api->reservePayment($this->dataOk);

    assertEquals(201, $payment['code']);

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
