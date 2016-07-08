<?php

/**
 * The MIT License (MIT)
 * Copyright (c) 2016 Angel Cruz <me@abr4xas.org>
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
 * @package php-instapago
 * @license MIT License
 * @copyright 2016 Angel Cruz
 */

namespace Instapago\InstapagoGateway;

use Instapago\InstapagoGateway\Exceptions\InstapagoException;

/**
 * Clase para la pasarela de pagos Instapago
 */

class InstapagoPayment
{

    protected 	$keyId;
    protected 	$publicKeyId;
    public 	  	$CardHolder;
    public  	$CardHolderId;
    public 		$CardNumber;
    public 		$CVC;
    public 		$ExpirationDate;
    public 		$Amount;
    public 		$Description;
    public 		$StatusId;
    public      $ip_addres;
    public      $idpago;
    public      $order_number;
    public      $address;
    public      $city;
    public      $zip_code;
    public      $state;
    public      $root = 'https://api.instapago.com/';

    /**
     * Crear un nuevo objeto de Instapago
     * @param string $keyId llave privada
     * @param string $publicKeyId llave publica
     * Requeridas.
     */
    public function __construct ($keyId,$publicKeyId)
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

    } // end construct

    /**
     * Crear un pago
     * Efectúa un pago con tarjeta de crédito, una vez procesado retornar una respuesta.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#crear-un-pago
     */
    public function payment($Amount,$Description,$CardHolder,$CardHolderId,$CardNumber,$CVC,$ExpirationDate,$StatusId,$ip_addres)
    {
        try {

            if (empty($Amount) || empty($Description) || empty($CardHolder) || empty($CardHolderId) || empty($CardNumber) || empty($CVC) || empty($ExpirationDate) || empty($StatusId) || empty($ip_addres)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->Amount           = $Amount;
            $this->Description      = $Description;
            $this->CardHolder       = $CardHolder;
            $this->CardHolderId     = $CardHolderId;
            $this->CardNumber       = $CardNumber;
            $this->CVC 			    = $CVC;
            $this->ExpirationDate   = $ExpirationDate;
            $this->StatusId		    = $StatusId;
            $this->ip_addres        = $ip_addres;

            $url = $this->root . 'payment'; // endpoint

            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "Amount"            => $this->Amount, //required
                "Description"       => $this->Description, //required
                "CardHolder"        => $this->CardHolder, //required
                "CardHolderId"      => $this->CardHolderId, //required
                "CardNumber"        => $this->CardNumber, //required
                "CVC"               => $this->CVC, //required
                "ExpirationDate"    => $this->ExpirationDate, //required
                "StatusId"          => $this->StatusId, //required
                "IP"                => $this->ip_addres //required
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;

        } catch (InstapagoException $e) {

            echo $e->getMessage();

        } // end try/catch

        return;

    } // end payment

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     * Para usar este método es necesario configurar en `payment()` el parametro StatusId a 1
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#completar-pago
     */

    public function continuePayment($Amount,$idpago)
    {
        try {

            if (empty($Amount) || empty($idpago)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->Amount = $Amount;
            $this->idpago = $idpago;

            $url = $this->root . 'complete'; // endpoint

            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "Amount"            => $this->Amount, //required
                "id"                => $this->idpago, //required
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;

        } catch (InstapagoException $e) {

            echo $e->getMessage();

        } // end try/catch

        return;
    } // continuePayment

    /**
     * Anular Pago
     * Este método funciona para procesar una anulación de un pago o un bloqueo.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#anular-pago
     */

    public function cancelPayment($idpago)
    {
        try {

            if (empty($idpago)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->idpago = $idpago;

            $url = $this->root . 'payment'; // endpoint

            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "id"                => $this->idpago, //required
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;

        } catch (InstapagoException $e) {

            echo $e->getMessage();

        } // end try/catch

        return;
    } // cancelPayment

    /**
     * Información del Pago
     * Consulta información sobre un pago generado anteriormente.
     * Requiere como parámetro el `id` que es el código de referencia de la transacción
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#información-del-pago
     */

    public function paymentInfo($idpago)
    {
        try {

            if (empty($idpago)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->idpago = $idpago;

            $url = $this->root . 'payment'; // endpoint

            $myCurl = curl_init();
            curl_setopt($myCurl, CURLOPT_URL, $url.'?'.'KeyID='. $this->keyId .'&PublicKeyId='. $this->publicKeyId .'&id=' . $this->idpago);
            curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, 1);
            $server_output = curl_exec($myCurl);
            curl_close ($myCurl);
            $obj = json_decode($server_output);
            $result = $this->checkResponseCode($obj);

            return $result;

        } catch (InstapagoException $e) {

            echo $e->getMessage();

        } // end try/catch

        return;
    } // paymentInfo

    /**
     * Crear un pago con parámetros opcionales
     * Efectúa un pago con tarjeta de crédito, una vez procesado retornar una respuesta.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#parámetros-opcionales-para-crear-el-pago
     */

    public function fullPayment($Amount,$Description,$CardHolder,$CardHolderId,$CardNumber,$CVC,$ExpirationDate,$StatusId,$ip_addres,$order_number,$address,$city,$zip_code,$state)
    {
        try {

            if (empty($Amount) || empty($Description) || empty($CardHolder) || empty($CardHolderId) || empty($CardNumber) || empty($CVC) || empty($ExpirationDate) || empty($StatusId) || empty($ip_addres) || empty($order_number) || empty($address) || empty($city) || empty($zip_code) || empty($state)) {
                throw new InstapagoException('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->Amount           = $Amount;
            $this->Description      = $Description;
            $this->CardHolder       = $CardHolder;
            $this->CardHolderId     = $CardHolderId;
            $this->CardNumber       = $CardNumber;
            $this->CVC              = $CVC;
            $this->ExpirationDate   = $ExpirationDate;
            $this->StatusId         = $StatusId;
            $this->ip_addres        = $ip_addres;
            $this->order_number     = $order_number;
            $this->address          = $address;
            $this->city             = $city;
            $this->zip_code          = $zip_code;
            $this->state            = $state;

            $url = $this->root . 'payment'; // endpoint

            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "Amount"            => $this->Amount, //required
                "Description"       => $this->Description, //required
                "CardHolder"        => $this->CardHolder, //required
                "CardHolderId"      => $this->CardHolderId, //required
                "CardNumber"        => $this->CardNumber, //required
                "CVC"               => $this->CVC, //required
                "ExpirationDate"    => $this->ExpirationDate, //required
                "StatusId"          => $this->StatusId, //required
                "IP"                => $this->ip_addres, //required
                "order_number"      => $this->order_number, // optional
                "address"           => $this->address, // optional
                "city"              => $this->city, // optional
                "zip_code"          => $this->zip_code, // optional
                "state"             => $this->state // optional
            ];

            $obj = $this->curlTransaccion($url, $fields);
            $result = $this->checkResponseCode($obj);

            return $result;

        } catch (InstapagoException $e) {

            echo $e->getMessage();

        } // end try/catch

        return;

    } // end payment

    /**
     * Realiza Transaccion
     * Efectúa y retornar una respuesta a un metodo de pago.
     *@param $url endpoint a consultar
     *@param $fields datos para la consulta
     *@return $obj array resultados de la transaccion
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
     */
    public function curlTransaccion($url, $fields)
    {
      $myCurl = curl_init();
      curl_setopt($myCurl, CURLOPT_URL,$url );
      curl_setopt($myCurl, CURLOPT_POST, 1);
      curl_setopt($myCurl, CURLOPT_POSTFIELDS,http_build_query($fields));
      curl_setopt($myCurl, CURLOPT_RETURNTRANSFER, true);
      $server_output = curl_exec ($myCurl);
      curl_close ($myCurl);
      $obj = json_decode($server_output);
      return $obj;
    }

    /**
     * Verifica Codigo de Estado de transaccion
     * Verifica y retornar el resultado de la transaccion.
     *@param $obj datos de la consulta
     *@return $result array datos de transaccion
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#PENDIENTE
     */
    public function checkResponseCode($obj)
    {
      $code = $obj->code;

      if ($code == 400) {
          throw new InstapagoException('Error al validar los datos enviados.');
      }elseif ($code == 401) {
          throw new InstapagoException('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
      }elseif ($code == 403) {
          throw new InstapagoException('Pago Rechazado por el banco.');
      }elseif ($code == 500) {
          throw new InstapagoException('Ha Ocurrido un error interno dentro del servidor.');
      }elseif ($code == 503) {
          throw new InstapagoException('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
      }elseif ($code == 201) {
        return [
            'code'      => $code ,
            'msg_banco' => $obj->message,
            'voucher' 	=> html_entity_decode($obj->voucher),
            'id_pago'	  => $obj->id,
            'reference' =>$obj->reference
        ];
      }
    }

} // end class
