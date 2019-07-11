<?php

/**
 * The MIT License (MIT)
 * Copyright (c) 2016 Angel Cruz <me@abr4xas.org>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author Angel Cruz <me@abr4xas.org>
 * @license MIT License
 * @copyright 2016 Angel Cruz
 */
use Instapago\Api;
use PHPUnit\Framework\TestCase as TestCase;

class ApiTest extends TestCase
{
    protected $api;
    protected $pago;

    protected function setUp()
    {
        $this->api = new Api(
            'D99BF283-A630-4168-9FED-6D2DA7E38DCA',
            'e9a5893e047b645fed12c82db877e05a'
        );
    }

    private function _dataPagoPrueba()
    {
        return [
            'amount'         => '200',
            'description'    => 'PHPUnit Test Payment',
            'card_holder'    => 'juan peñalver',
            'card_holder_id' => '11111111',
            'card_number'    => '4111111111111111',
            'cvc'            => '123',
            'expiration'     => '12/2020',
            'ip'             => '127.0.0.1',
        ];
    }

    private function _dataPagoPruebaError()
    {
        return [
            'amount'         => '200.00',
            'description'    => 'PHPUnit Test Payment',
            'card_holder'    => 'juan peñalver',
            'card_holder_id' => '11111111',
            'card_number'    => '4111111111111112',
            'cvc'            => '123',
            'expiration'     => '12/2020',
            'ip'             => '127.0.0.1',
        ];
    }

    public function testBadData()
    {
        try {
            $data = $this->_dataPagoPruebaError();
            $pago = $this->api->directPayment($data);
        } catch (\Instapago\Exceptions\InvalidInputException $e) {
            $this->assertContains('Error al validar los datos enviados', $e->getMessage());
        }
    }

    public function testCreaPagoDirecto()
    {
        $data = $this->_dataPagoPrueba();
        $pago = $this->api->directPayment($data);
        $this->assertEquals(201, $pago['code']);

        return $pago;
    }

    public function testCreaPagoReserva()
    {
        $data = $this->_dataPagoPrueba();
        $pago = $this->api->reservePayment($data);
        $this->assertEquals(201, $pago['code']);
        $this->assertContains('pago aprobado', strtolower($pago['msg_banco']));

        return $pago;
    }

    /**
     * @depends testCreaPagoReserva
     */
    public function testContinuarPago($pago)
    {
        $continue = $this->api->continuePayment([
            'id'     => $pago['id_pago'],
            'amount' => '200',
        ]);

        $this->assertContains('pago completado', strtolower($continue['msg_banco']));
    }

    /**
     * @depends testCreaPagoDirecto
     */
    public function testInfoPago($pago)
    {
        $info = $this->api->query($pago['id_pago']);
        $this->assertContains('autorizada', strtolower($info['msg_banco']));
    }

    /**
     * @depends testCreaPagoDirecto
     * En modo pruebas este método no funciona.
     * El personal de instapago asegura que en producción no hay problemas
     */
    public function testCancelPago($pago)
    {
        $info = $this->api->cancel($pago['id_pago']);
        $this->assertContains('el pago ha sido anulado', strtolower($info['msg_banco']));
    }
}
