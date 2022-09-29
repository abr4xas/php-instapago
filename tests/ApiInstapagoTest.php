<?php

namespace Instapago\Instapago\Tests;

use Instapago\Instapago\Api;
use Instapago\Instapago\Exceptions\InvalidInputException;
use Instapago\Instapago\Exceptions\ValidationException;
use PHPUnit\Framework\TestCase;

class ApiInstapagoTest extends TestCase
{
    protected Api $api;
//    protected $pago;

    protected function setUp(): void
    {
        $this->api = new Api('1E488391-7934-4301-9F8E-17DC99AB49B3', '691f77db9d62c0f2fe191ce69ed9bb41');
    }

    private function _dataPagoPrueba()
    {
        return [
            'amount' => '200',
            'description' => 'PHPUnit Test Payment',
            'card_holder' => 'juan peñalver',
            'card_holder_id' => '11111111',
            'card_number' => '4111111111111111',
            'cvc' => '123',
            'expiration' => '12/2026',
            'ip' => '127.0.0.1',
        ];
    }

    private function _dataPagoPruebaError()
    {
        return [
            'amount' => '200.00',
            'description' => 'PHPUnit Test Payment',
            'card_holder' => 'juan peñalver',
            'card_holder_id' => '11111111',
            'card_number' => '4111111111111112',
            'cvc' => '123',
            'expiration' => '12/2020',
            'ip' => '127.0.0.1',
        ];
    }

    /** @test */
    public function test_data_erronea()
    {
        try {
            $data = $this->_dataPagoPruebaError();
            $this->api->directPayment($data);
        } catch (InvalidInputException $e) {
            $this->assertStringContainsStringIgnoringCase('Error al validar los datos enviados', $e->getMessage());
        } catch (ValidationException $e) {
		}
	}

	/** @test
	 * @throws ValidationException
	 */
    public function test_crear_pago_directo()
    {
        $data = $this->_dataPagoPrueba();
        $pago = $this->api->directPayment($data);
        $this->assertEquals(201, $pago['code']);

        return $pago;
    }

	/** @test
	 * @throws ValidationException
	 */
    public function test_crear_pago_reserva()
    {
        $data = $this->_dataPagoPrueba();
        $pago = $this->api->reservePayment($data);
        $this->assertEquals(201, $pago['code']);
        $this->assertStringContainsStringIgnoringCase('pago aprobado', strtolower($pago['msg_banco']));

        return $pago;
    }

	/**
	 * @depends test_crear_pago_reserva
	 * @throws ValidationException
	 */
    public function testContinuarPago($pago)
    {
        $continue = $this->api->continuePayment([
            'id' => $pago['id_pago'],
            'amount' => '200',
        ]);

        $this->assertStringContainsStringIgnoringCase('pago completado', strtolower($continue['msg_banco']));
    }

	/**
	 * @depends test_crear_pago_directo
	 * @throws ValidationException
	 */
    public function test_info_pago($pago)
    {
        $info = $this->api->query($pago['id_pago']);
        $this->assertStringContainsStringIgnoringCase('autorizada', strtolower($info['msg_banco']));
    }

	/**
	 * @depends test_crear_pago_directo
	 * @throws ValidationException
	 */
    public function test_cancelar_pago($pago)
    {
        $info = $this->api->cancel($pago['id_pago']);
        $this->assertStringContainsStringIgnoringCase('el pago ha sido anulado', strtolower($info['message']));
    }
}
