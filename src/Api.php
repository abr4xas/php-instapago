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

namespace Instapago\Instapago;

use \Instapago\Instapago\Exceptions\AuthException;
use \Instapago\Instapago\Exceptions\BankRejectException;
use \Instapago\Instapago\Exceptions\GenericException;
use \Instapago\Instapago\Exceptions\InstapagoException;
use \Instapago\Instapago\Exceptions\InvalidInputException;
use \Instapago\Instapago\Exceptions\TimeoutException;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ConnectException;

/**
 * Clase para la pasarela de pagos Instapago.
 */
class Api
{
    protected string $keyId;
    protected string $publicKeyId;

    /**
     * Crear un nuevo objeto de Instapago.
     *
     * @param string $keyId       llave privada
     * @param string $publicKeyId llave publica
     *                            Requeridas.
     */
    public function __construct(string $keyId, string $publicKeyId)
    {
        if (empty($keyId) || empty($publicKeyId)) {
            throw new InstapagoException('Los parámetros "keyId" y "publicKeyId" son requeridos para procesar la petición.');
        }
        $this->publicKeyId = $publicKeyId;
        $this->keyId = $keyId;
    }

    /**
     * Crear un pago directo.
     *
     * @param array<string> $fields Los campos necesarios
     *                              para procesar el pago.
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    public function directPayment($fields)
    {
        return $this->payment('2', $fields);
    }

    /**
     * Crear un pago diferido o reservado.
     *
     * @param array<string> $fields Los campos necesarios
     *                              para procesar el pago.
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    public function reservePayment($fields)
    {
        return $this->payment('1', $fields);
    }

    /**
     * Crear un pago.
     *
     * @param string        $type   tipo de pago ('1' o '0')
     * @param array<string> $fields Los campos necesarios
     *                              para procesar el pago.
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    private function payment($type, $fields)
    {
        (new Validator())->payment()->validate($fields);

        $fields = [
            'KeyID' => $this->keyId,
            'PublicKeyId' => $this->publicKeyId,
            'amount' => $fields['amount'],
            'description' => $fields['description'],
            'cardHolder' => $fields['card_holder'],
            'cardHolderId' => $fields['card_holder_id'],
            'cardNumber' => $fields['card_number'],
            'cvc' => $fields['cvc'],
            'expirationDate' => $fields['expiration'],
            'statusId' => $type,
            'IP' => $fields['ip'],
        ];

        $obj = $this->curlTransaccion('payment', $fields, 'POST');
        $result = $this->checkResponseCode($obj);

        return $result;
    }

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     *
     * @param array<string> $fields Los campos necesarios
     *                              para procesar el pago.
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    public function continuePayment($fields)
    {
        (new Validator())->release()->validate($fields);
        $fields = [
            'KeyID' => $this->keyId, //required
            'PublicKeyId' => $this->publicKeyId, //required
            'id' => $fields['id'], //required
            'amount' => $fields['amount'], //required
        ];

        $obj = $this->curlTransaccion('complete', $fields, 'POST');
        $result = $this->checkResponseCode($obj);

        return $result;
    }

    /**
     * Información/Consulta de Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     *
     * @param string $id_pago ID del pago a consultar
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    public function query($id_pago)
    {
        (new Validator())->query()->validate([
            'id' => $id_pago,
        ]);

        $fields = [
            'KeyID' => $this->keyId, //required
            'PublicKeyId' => $this->publicKeyId, //required
            'id' => $id_pago, //required
        ];

        $obj = $this->curlTransaccion('payment', $fields, 'GET');
        $result = $this->checkResponseCode($obj);

        return $result;
    }

    /**
     * Cancelar Pago
     * Este método funciona para cancelar un pago previamente procesado.
     *
     * @param string $id_pago ID del pago a cancelar
     *
     * @throws Exceptions\InstapagoException
     *
     * @return array<string> Respuesta de Instapago
     */
    public function cancel($id_pago)
    {
        (new Validator())->query()->validate([
            'id' => $id_pago,
        ]);

        $fields = [
            'KeyID' => $this->keyId, //required
            'PublicKeyId' => $this->publicKeyId, //required
            'id' => $id_pago, //required
        ];

        $obj = $this->curlTransaccion('payment', $fields, 'DELETE');
        $result = $this->checkResponseCode($obj);

        return $result;
    }

    /**
     * Realiza Transaccion
     * Efectúa y retornar una respuesta a un metodo de pago.
     *
     * @param $url string endpoint a consultar
     * @param $method string verbo http de la consulta
     * @param (mixed|string)[] $fields datos para la consulta
     *
     * @return array resultados de la transaccion
     */
    public function curlTransaccion(string $url, array $fields, string $method)
    {
        $client = new Client([
            'base_uri' => 'https://api.instapago.com/',
        ]);

        $args = [];
        if (! in_array($method, ['GET', 'POST', 'DELETE'])) {
            throw new GenericException('Not implemented yet', 1);
        }
        $key = ($method == 'GET') ? 'query' : 'form_params';

        $args[$key] = $fields;

        try {
            $request = $client->request($method, $url, $args);
            $body = $request->getBody()->getContents();
            $obj = json_decode($body, true);

            return $obj;
        } catch (ConnectException $e) {
            throw new TimeoutException('Cannot connect to api.instapago.com');
        }
    }

    /**
     * Verifica y retornar el resultado de la transaccion.
     *
     * @param $obj datos de la consulta
     *
     * @return array datos de transaccion
     */
    public function checkResponseCode(array $obj)
    {
        $code = $obj['code'];

        switch ($code) {
            case 400:
                throw new InvalidInputException(
                    'Error al validar los datos enviados.'
                );

            case 401:
                throw new AuthException(
                    'Error de autenticación, ha ocurrido un error con las llaves utilizadas.'
                );

            case 403:
                throw new BankRejectException(
                    'Pago Rechazado por el banco.'
                );

            case 500:
                throw new InstapagoException(
                    'Ha Ocurrido un error interno dentro del servidor.'
                );

            case 503:
                throw new InstapagoException(
                    'Ha Ocurrido un error al procesar los parámetros de entrada.  Revise los datos enviados y vuelva a intentarlo.'
                );

            case 201:
                return [
                    'code' => $code,
                    'msg_banco' => $obj['message'],
                    'voucher' => html_entity_decode($obj['voucher']),
                    'id_pago' => $obj['id'],
                    'reference' => $obj['reference'],
                    'original_response' => $obj,
                ];

            default:
            throw new GenericException('Not implemented yet');

        }
    }
}
