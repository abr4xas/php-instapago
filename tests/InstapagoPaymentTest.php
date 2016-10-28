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

require_once 'Instapago/autoload.php';

use \Instapago\InstapagoGateway\InstapagoPayment;
use \PHPUnit_Framework_TestCase as Test;

class InstapagoPaymentTest extends Test
{
    protected $api;
    protected $pago;

    protected function setUp()
    {
        $this->api = new InstapagoPayment('74D4A278-C3F8-4D7A-9894-FA0571D7E023', 'e9a5893e047b645fed12c82db877e05a');
    }

    public function testCreaPago()
    {
        $this->pago = $this->api->payment('200', 'test', 'jon doe', '11111111', '4111111111111111', '123', '12/2020', '1', '127.0.0.1');
        $this->assertEquals(201, $this->pago['code']);

        return $this->pago;
    }

    /**
     * @depends testCreaPago
     */
    public function testContinuarPago($pago)
    {
        $this->pago = $pago;
        $continue = $this->api->continuePayment($this->pago['id_pago'],'200');
        $this->assertContains('Pago Completado', $continue['msg_banco'], '', true);
    }

    /**
     * @depends testCreaPago
     */
    public function testInfoPago($pago)
    {
        $this->pago = $pago;
        $info = $this->api->paymentInfo($this->pago['id_pago']);
        $this->assertContains('Completada', $info['msg_banco']);
    }

    /**
     * @depends testCreaPago
     * En modo pruebas este método no funciona. 
     * El personal de instapago asegura que en producción no hay problemas
     */
    public function testCancelPago(array $pago)
    {
        $this->pago = $pago;
        $info = $this->api->cancelPayment($this->pago['id_pago']);
        $this->assertContains('El pago ha sido anulado', $info['msg_banco']);
    }
}
