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
namespace Instapago\InstapagoGateway;

use Instapago\InstapagoGateway\Exceptions\InstapagoException;

/**
 * Clase para la pasarela de pagos Instapago.
 */
class InstapagoPayment
{
    protected $keyId;
    protected $publicKeyId;
    public $cardHolder;
    public $cardHolderId;
    public $cardNumber;
    public $cvc;
    public $expirationDate;
    public $amount;
    public $description;
    public $statusId;
    public $ipAddres;
    public $idPago;
    public $root = 'https://api.instapago.com/';

    /**
     * Crear un nuevo objeto de Instapago.
     *
     * @param string $keyId       llave privada
     * @param string $publicKeyId llave publica
     *                            Requeridas.
     */
    public function __construct($keyId, $publicKeyId)
    {
        try {
            if (empty($keyId) && empty($publicKeyId)) {
                throw new InstapagoException('Los parámetros "keyId" y "publicKeyId" son requeridos para procesar la petición.');
            }

            if (empty($keyId)) {
                throw new InstapagoException('El parámetro "keyId" es requerido para procesar la petición. ');
            }

            if (empty($publicKeyId)) {
                throw new InstapagoException('El parámetro "publicKeyId" es requerido para procesar la petición.');
            }

            $this->publicKeyId = $publicKeyId;
            $this->keyId = $keyId;
        } catch (InstapagoException $e) {
            echo $e->getMessage();
        } // end try/catch
    }

 // end construct

    /**
     * Crear un pago
     * Efectúa un pago con tarjeta de crédito, una vez procesado retornar una respuesta.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#crear-un-pago.
     */
    public function payment($amount, $description, $cardHolder, $cardHolderId, $cardNumber, $cvc, $expirationDate, $statusId, $ipAddres)
    {
        try {
            $params = [$amount, $description, $cardHolder, $cardHolderId, $cardNumber, $cvc, $expirationDate, $statusId, $ipAddres];
            $this->checkRequiredParams($params);

            $this->amount = $amount;
            $this->description = $description;
            $this->cardHolder = $cardHolder;
            $this->cardHolderId = $cardHolderId;
            $this->cardNumber = $cardNumber;
            $this->cvc = $cvc;
            $this->expirationDate = $expirationDate;
            $this->statusId = $statusId;
            $this->ipAddres = $ipAddres;

            $url = $this->root.'payment'; // endpoint

            $fields = [
                'KeyID'             => $this->keyId, //required
                'PublicKeyId'       => $this->publicKeyId, //required
                'amount'            => $this->amount, //required
                'description'       => $this->description, //required
                'cardHolder'        => $this->cardHolder, //required
                'cardHolderId'      => $this->cardHolderId, //required
                'cardNumber'        => $this->cardNumber, //required
                'cvc'               => $this->cvc, //required
                'expirationDate'    => $this->expirationDate, //required
                'statusId'          => $this->statusId, //required
                'IP'                => $this->ipAddres, //required
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;
        } catch (InstapagoException $e) {
            echo $e->getMessage();
        } // end try/catch
    }

 // end payment

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     * Para usar este método es necesario configurar en `payment()` el parametro statusId a 1
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#completar-pago.
     */

    public function continuePayment($idPago, $amount)
    {
        try {
            $params = [$idPago, $amount];
            $this->checkRequiredParams($params);

            $this->idPago = $idPago;
            $this->amount = $amount;

            $url = $this->root.'complete'; // endpoint

            $fields = [
                'KeyID'             => $this->keyId, //required
                'PublicKeyId'       => $this->publicKeyId, //required
                'id'                => $this->idPago, //required
                'amount'            => $this->amount, //required
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;
        } catch (InstapagoException $e) {
            echo $e->getMessage();
        } // end try/catch
    }

 // continuePayment

    /**
     * Anular Pago
     * Este método funciona para procesar una anulación de un pago o un bloqueo.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#anular-pago.
     */
    public function cancelPayment($idPago)
    {
        try {
            // TODO
        } catch (InstapagoException $e) {
            echo $e->getMessage();
        } // end try/catch
    }

 // cancelPayment

    /**
     * Información del Pago
     * Consulta información sobre un pago generado anteriormente.
     * Requiere como parámetro el `id` que es el código de referencia de la transacción
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#información-del-pago.
     */
    public function paymentInfo($idPago)
    {
        try {
            $params = [$idPago];
            $this->checkRequiredParams($params);

            $this->idPago = $idPago;

            $url = $this->root.'payment'; // endpoint

            $myCurl = curl_init();
            curl_setopt($myCurl, CURLOPT_URL, $url.'?'.'KeyID='.$this->keyId.'&PublicKeyId='.$this->publicKeyId.'&id='.$this->idPago);
            curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
            $server_output = curl_exec($myCurl);
            curl_close($myCurl);
            $obj = json_decode($server_output);
            $result = $this->checkResponseCode($obj);

            return $result;
        } catch (InstapagoException $e) {
            echo $e->getMessage();
        } // end try/catch
    }

 // paymentInfo

    /**
     * Realiza Transaccion
     * Efectúa y retornar una respuesta a un metodo de pago.
     *
     *@param $url endpoint a consultar
     *@param $fields datos para la consulta
     *
     *@return $obj array resultados de la transaccion
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
     */
    public function curlTransaccion($url, $fields)
    {
        $myCurl = curl_init();
        curl_setopt($myCurl, CURLOPT_URL, $url);
        curl_setopt($myCurl, CURLOPT_POST, 1);
        curl_setopt($myCurl, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($myCurl);
        curl_close($myCurl);
        $obj = json_decode($server_output);

        return $obj;
    }

    /**
     * Verifica Codigo de Estado de transaccion
     * Verifica y retornar el resultado de la transaccion.
     *
     *@param $obj datos de la consulta
     *
     *@return $result array datos de transaccion
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
     */
    public function checkResponseCode($obj)
    {
        $code = $obj->code;

        if ($code == 400) {
            throw new InstapagoException('Error al validar los datos enviados.');
        } elseif ($code == 401) {
            throw new InstapagoException('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
        } elseif ($code == 403) {
            throw new InstapagoException('Pago Rechazado por el banco.');
        } elseif ($code == 500) {
            throw new InstapagoException('Ha Ocurrido un error interno dentro del servidor.');
        } elseif ($code == 503) {
            throw new InstapagoException('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
        } elseif ($code == 201) {
            return [
            'code'         => $code,
            'msg_banco'    => $obj->message,
            'voucher'      => html_entity_decode($obj->voucher),
            'id_pago'      => $obj->id,
            'reference'    => $obj->reference,
        ];
        }
    }

    /**
     * Verifica parametros para realizar operación
     * Verifica y retorna exception si algun parametro esta vacio.
     *
     *@param $params Array con parametros a verificar
     *
     *@return new InstapagoException
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
     */
    private function checkRequiredParams(array $params)
    {
        foreach ($params as $param) {
            if (empty($param)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }
        }
    }
} // end class
