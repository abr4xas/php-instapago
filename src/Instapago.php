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
*/


class Instapago
{

    protected 	$keyId;
    protected 	$publicKeyId;
    public 		$CardHolder;
    public 		$CardHolderId;
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
                throw new Exception('Los parámetros "keyId" y "publicKeyId" son requeridos para procesar la petición.');
            }elseif (empty($keyId)) {
                throw new Exception('El parámetro "keyId" es requerido para procesar la petición. sss');
            }else{
                $this->keyId = $keyId;
            }
            if (empty($publicKeyId)) {
                throw new Exception('El parámetro "publicKeyId" es requerido para procesar la petición.');
            }else{
                $this->publicKeyId = $publicKeyId;
            }
        } catch (Exception $e) {
            echo '<pre>Message: ' . $e->getMessage() . '</pre>';
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
            if (empty($Amount) && empty($Description) &&
                empty($CardHolder) && empty($CardHolderId) &&
                empty($CardNumber) && empty($CVC) &&
                empty($ExpirationDate) && empty($StatusId) && empty($ip_addres)) {
                throw new Exception('Parámetros faltantes para procesar el pago. Verifique la documentación.');
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

            $url = 'https://api.instapago.com/payment'; // endpoint
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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $obj = json_decode($server_output);
            $code = $obj->code;

            if ($code == 400) {
                throw new Exception('Error al validar los datos enviados.');
            }elseif ($code == 401) {
                throw new Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            }elseif ($code == 403) {
                throw new Exception('Pago Rechazado por el banco.');
            }elseif ($code == 500) {
                throw new Exception('Ha Ocurrido un error interno dentro del servidor.');
            }elseif ($code == 503) {
                throw new Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            }elseif ($code == 201) {
                $msg_banco  = $obj->message;
                $voucher    = $obj->voucher;
                $voucher    = html_entity_decode($voucher);
                $id_pago    = $obj->id;
                $reference  = $obj->reference;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } // end try/catch

        return array(
            'msg_banco' => $msg_banco,
            'voucher' 	=> $voucher,
            'id_pago'	=> $id_pago,
            'reference' => $reference
        );

    } // end payment

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización, para así procesarla y hacer el cobro respectivo.
     * Para usar este método es necesario configurar en payment() el parametro StatusId a 1
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#completar-pago
     */

    public function continuePayment($Amount,$idpago)
    {
        try {
            if (empty($Amount) && empty($idpago)) {
                throw new Exception('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->Amount = $Amount;
            $this->idpago = $idpago;

            $url = 'https://api.instapago.com/complete'; // endpoint
            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "Amount"            => $this->Amount, //required
                "id"                => $this->idpago, //required
            ];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $obj = json_decode($server_output);
            $code = $obj->code;

            if ($code == 400) {
                throw new Exception('Error al validar los datos enviados.');
            }elseif ($code == 401) {
                throw new Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            }elseif ($code == 403) {
                throw new Exception($obj->message);
            }elseif ($code == 500) {
                throw new Exception('Ha Ocurrido un error interno dentro del servidor.');
            }elseif ($code == 503) {
                throw new Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            }elseif ($code == 201) {
                $msg_banco  = $obj->message;
                $voucher    = $obj->voucher;
                $voucher    = html_entity_decode($voucher);
                $id_pago    = $obj->id;
                $reference  = $obj->reference;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } // end try/catch
        return array(
            'msg_banco' => $msg_banco,
            'voucher' 	=> $voucher,
            'id_pago'	=> $id_pago,
            'reference' => $reference
        );
    } // continuePayment

    /**
     * Anular Pago
     * Este método funciona para procesar una anulación de un pago, ya sea un pago o un bloqueo.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#anular-pago
     */

    public function cancelPayment($idpago)
    {
        try {
            if (empty($idpago)) {
                throw new Exception('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->idpago = $idpago;

            $url = 'https://api.instapago.com/payment'; // endpoint
            $fields = [
                "KeyID"             => $this->keyId, //required
                "PublicKeyId"       => $this->publicKeyId, //required
                "id"                => $this->idpago, //required
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $obj = json_decode($server_output);
            $code = $obj->code;

            if ($code == 400) {
                throw new Exception('Error al validar los datos enviados.');
            }elseif ($code == 401) {
                throw new Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            }elseif ($code == 500) {
                throw new Exception('Ha Ocurrido un error interno dentro del servidor.');
            }elseif ($code == 503) {
                throw new Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            }elseif ($code == 201) {
                $msg_banco  = $obj->message;
                $voucher    = $obj->voucher;
                $voucher    = html_entity_decode($voucher);
                $id_pago    = $obj->id;
                $reference  = $obj->reference;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } // end try/catch
        return array(
            'msg_banco' => $msg_banco,
            'voucher' 	=> $voucher,
            'id_pago'	=> $id_pago,
            'reference' => $reference
        );
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
                throw new Exception('Parámetros faltantes para procesar el pago. Verifique la documentación.');
            }

            $this->idpago = $idpago;

            $url = 'https://api.instapago.com/payment'; // endpoint
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url.'?'.'KeyID='. $this->keyId .'&PublicKeyId='. $this->publicKeyId .'&id=' . $this->idpago);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $server_output = curl_exec($ch);
            curl_close ($ch);
            $obj = json_decode($server_output);
            $code = $obj->code;

            if ($code == 400) {
                throw new Exception('Error al validar los datos enviados.');
            }elseif ($code == 401) {
                throw new Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            }elseif ($code == 500) {
                throw new Exception('Ha Ocurrido un error interno dentro del servidor.');
            }elseif ($code == 503) {
                throw new Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            }elseif ($code == 201) {
                $msg_banco  = $obj->message;
                $voucher    = $obj->voucher;
                $voucher    = html_entity_decode($voucher);
                $id_pago    = $obj->id;
                $reference  = $obj->reference;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } // end try/catch
        return array(
            'msg_banco' => $msg_banco,
            'voucher' 	=> $voucher,
            'id_pago'	=> $id_pago,
            'reference' => $reference
        );
    } // paymentInfo

    /**
     * Crear un pago con parámetros opcionales
     * Efectúa un pago con tarjeta de crédito, una vez procesado retornar una respuesta.
     * https://github.com/abr4xas/php-instapago/blob/master/help/DOCUMENTACION.md#parámetros-opcionales-para-crear-el-pago
     */

    public function fullPayment($Amount,$Description,$CardHolder,$CardHolderId,$CardNumber,$CVC,$ExpirationDate,$StatusId,$ip_addres,$order_number,$address,$city,$zip_code,$state)
    {
        try {
            if (empty($Amount) && empty($Description) &&
                empty($CardHolder) && empty($CardHolderId) &&
                empty($CardNumber) && empty($CVC) &&
                empty($ExpirationDate) && empty($StatusId) && empty($ip_addres) && empty($order_number)
                empty($address) && empty($city) && empty($zip_code) && empty($state)) {
                throw new Exception('Parámetros faltantes para procesar el pago. Verifique la documentación.');
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
            $thi->zip_code          = $zip_code;
            $this->state            = $state;

            $url = 'https://api.instapago.com/payment'; // endpoint
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
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$url );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            $obj = json_decode($server_output);
            $code = $obj->code;

            if ($code == 400) {
                throw new Exception('Error al validar los datos enviados.');
            }elseif ($code == 401) {
                throw new Exception('Error de autenticación, ha ocurrido un error con las llaves utilizadas.');
            }elseif ($code == 403) {
                throw new Exception('Pago Rechazado por el banco.');
            }elseif ($code == 500) {
                throw new Exception('Ha Ocurrido un error interno dentro del servidor.');
            }elseif ($code == 503) {
                throw new Exception('Ha Ocurrido un error al procesar los parámetros de entrada. Revise los datos enviados y vuelva a intentarlo.');
            }elseif ($code == 201) {
                $msg_banco  = $obj->message;
                $voucher    = $obj->voucher;
                $voucher    = html_entity_decode($voucher);
                $id_pago    = $obj->id;
                $reference  = $obj->reference;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        } // end try/catch

        return array(
            'msg_banco' => $msg_banco,
            'voucher'   => $voucher,
            'id_pago'   => $id_pago,
            'reference' => $reference
        );

    } // end payment

} // end class
