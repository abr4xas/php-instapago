<?php

namespace Instapago\Instapago;

use GuzzleHttp\Client as Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use Instapago\Instapago\Exceptions\AuthException;
use Instapago\Instapago\Exceptions\BankRejectException;
use Instapago\Instapago\Exceptions\GenericException;
use Instapago\Instapago\Exceptions\InstapagoException;
use Instapago\Instapago\Exceptions\InvalidInputException;
use Instapago\Instapago\Exceptions\TimeoutException;

/**
 * Clase para la pasarela de pagos Instapago.
 */
class Api
{
    /**
     * Crear un nuevo objeto de Instapago.
     *
     * @param  string  $keyId llave privada
     * @param  string  $publicKeyId llave publica
     *                            Requeridas.
     *
     * @throws InstapagoException
     */
    public function __construct(protected string $keyId, protected string $publicKeyId)
    {
        if (empty($keyId) || empty($publicKeyId)) {
            throw new InstapagoException('Los parámetros "keyId" y "publicKeyId" son requeridos para procesar la petición.');
        }
    }

    /**
     * Crear un pago directo.
     */
    public function directPayment(array $fields): array|string
    {
        try {
            return $this->payment('2', $fields);
        } catch (AuthException|BankRejectException|GenericException|InstapagoException|InvalidInputException|TimeoutException|Exceptions\ValidationException|GuzzleException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Crear un pago diferido o reservado.
     */
    public function reservePayment($fields): array|string
    {
        try {
            return $this->payment('1', $fields);
        } catch (AuthException|BankRejectException|GenericException|InstapagoException|InvalidInputException|TimeoutException|Exceptions\ValidationException|GuzzleException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     *
     * @throws Exceptions\ValidationException
     * @throws GenericException
     * @throws GuzzleException
     * @throws TimeoutException
     */
    public function completePayment(array $fields): array|string
    {
        (new Validator())->release()->validate($fields);

        $fields = [
            'KeyID' => $this->keyId, //required
            'PublicKeyId' => $this->publicKeyId, //required
            'id' => $fields['id'], //required
            'amount' => $fields['amount'], //required
        ];

        $obj = $this->curlTransaccion('complete', $fields, 'POST');

        try {
            return $this->checkResponseCode($obj);
        } catch (AuthException|BankRejectException|GenericException|InstapagoException|InvalidInputException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Información/Consulta de Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     *
     * @throws Exceptions\ValidationException
     * @throws GenericException
     * @throws GuzzleException
     * @throws TimeoutException
     */
    public function query(string $id_pago): array|string
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

        try {
            return $this->checkResponseCode($obj);
        } catch (AuthException|BankRejectException|GenericException|InstapagoException|InvalidInputException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Cancelar Pago
     * Este método funciona para cancelar un pago previamente procesado.
     *
     * @throws Exceptions\ValidationException
     */
    public function cancel(string $id_pago): array|string
    {
        (new Validator())->query()->validate([
            'id' => $id_pago,
        ]);

        $fields = [
            'KeyID' => $this->keyId, //required
            'PublicKeyId' => $this->publicKeyId, //required
            'id' => $id_pago, //required
        ];

        try {
            return $this->curlTransaccion('payment', $fields, 'DELETE');
        } catch (GuzzleException|GenericException|TimeoutException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Crear un pago.
     *
     * @param  string  $type   tipo de pago ('1' o '0')
     *
     * @throws AuthException
     * @throws BankRejectException
     * @throws Exceptions\ValidationException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InvalidInputException
     * @throws TimeoutException|GuzzleException
     */
    private function payment(string $type, array $fields): array
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

        return $this->checkResponseCode($obj);
    }

    /**
     * Realiza Transaccion
     * Efectúa y retornar una respuesta a un metodo de pago.
     *
     * @param $url string endpoint a consultar
     * @param $method string verbo http de la consulta
     * @return array resultados de la transaccion
     *
     * @throws GenericException
     * @throws TimeoutException
     * @throws GuzzleException
     */
    private function curlTransaccion(string $url, array $fields, string $method): array
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

            return json_decode($body, true);
        } catch (ConnectException $e) {
            throw new TimeoutException('Cannot connect to api.instapago.com');
        }
    }

    /**
     * Verifica y retornar el resultado de la transaccion.
     *
     * @param  array  $obj datos de la consulta
     * @return array datos de transaccion
     *
     * @throws AuthException
     * @throws BankRejectException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InvalidInputException
     */
    private function checkResponseCode(array $obj): array
    {

        return match ($obj['code']) {
            '400' => throw new InvalidInputException(
                'Error al validar los datos enviados'
            ),
            '401' => throw new AuthException(
                'Error de autenticación, ha ocurrido un error con las llaves utilizadas'
            ),
            '403' => throw new BankRejectException(
                'Pago Rechazado por el banco'
            ),
            '500' => throw new InstapagoException(
                'Ha Ocurrido un error interno dentro del servidor'
            ),
            '503' => throw new InstapagoException(
                'Ha Ocurrido un error al procesar los parámetros de entrada.  Revise los datos enviados y vuelva a intentarlo'
            ),
            '201' => $this->getResponse($obj),
            default => throw new GenericException(
                'Not implemented yet'
            ),
        };

    }

    private function getResponse(array $obj): array
    {
        return [
            'code' => $obj['code'],
            'message' => $obj['message'],
            'voucher' => html_entity_decode($obj['voucher']),
            'id_pago' => $obj['id'],
            'reference' => $obj['reference'],
            'original_response' => $obj,
        ];
    }
}
