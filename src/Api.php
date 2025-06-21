<?php

declare(strict_types=1);

namespace Instapago\Instapago;

use Instapago\Instapago\Enums\PaymentType;
use Instapago\Instapago\Exceptions\GenericException;
use Instapago\Instapago\Exceptions\InstapagoAuthException;
use Instapago\Instapago\Exceptions\InstapagoBankRejectException;
use Instapago\Instapago\Exceptions\InstapagoException;
use Instapago\Instapago\Exceptions\InstapagoInvalidInputException;
use Instapago\Instapago\Exceptions\InstapagoTimeoutException;
use Instapago\Instapago\Exceptions\ValidationException;
use Instapago\Instapago\Http\GuzzleHttpClient;
use Instapago\Instapago\Http\HttpClientInterface;
use Instapago\Instapago\Logging\LoggerInterface;
use Instapago\Instapago\Logging\NullLogger;
use Instapago\Instapago\Services\ResponseHandler;
use Instapago\Instapago\Validation\CompletePaymentValidationStrategy;
use Instapago\Instapago\Validation\PaymentValidationStrategy;
use Instapago\Instapago\Validation\QueryValidationStrategy;

/**
 * Clase para la pasarela de pagos Instapago.
 */
final class Api implements InstapagoApiInterface
{
    private HttpClientInterface $httpClient;

    private ResponseHandler $responseHandler;

    private Validator $validator;

    private LoggerInterface $logger;

    /**
     * Crear un nuevo objeto de Instapago.
     *
     * @param  string  $keyId  llave privada
     * @param  string  $publicKeyId  llave pública
     * @param  HttpClientInterface|null  $httpClient  cliente HTTP personalizado
     *
     * @throws InstapagoException
     */
    public function __construct(
        protected string $keyId,
        protected string $publicKeyId,
        ?HttpClientInterface $httpClient = null,
        ?LoggerInterface $logger = null
    ) {
        if (empty($keyId) || empty($publicKeyId)) {
            throw new InstapagoException('Los parámetros "keyId" y "publicKeyId" son requeridos para procesar la petición.');
        }

        $this->httpClient = $httpClient ?? new GuzzleHttpClient();
        $this->responseHandler = new ResponseHandler();
        $this->validator = new Validator();
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * Crear un pago directo.
     *
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     * @throws InstapagoTimeoutException
     * @throws ValidationException
     */
    public function directPayment(array $fields): array
    {
        return $this->processPayment(PaymentType::DIRECT, $fields);
    }

    /**
     * Crear un pago diferido o reservado.
     *
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     * @throws InstapagoTimeoutException
     * @throws ValidationException
     */
    public function reservePayment(array $fields): array
    {
        return $this->processPayment(PaymentType::RESERVED, $fields);
    }

    /**
     * Completar Pago
     * Este método funciona para procesar un bloqueo o pre-autorización
     * para así procesarla y hacer el cobro respectivo.
     *
     * @throws ValidationException
     * @throws GenericException
     * @throws InstapagoTimeoutException
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     */
    public function completePayment(array $fields): array
    {
        $this->logger->info('Completando pago', ['payment_id' => $fields['id'] ?? 'N/A']);

        $validationStrategy = new CompletePaymentValidationStrategy();
        $validationStrategy->validate($fields);

        $requestData = [
            'KeyID' => $this->keyId,
            'PublicKeyId' => $this->publicKeyId,
            'id' => $fields['id'],
            'amount' => $fields['amount'],
        ];

        $this->logger->logRequest('POST', 'complete', $requestData);

        try {
            $response = $this->httpClient->request('POST', 'complete', $requestData);
            $this->logger->logResponse($response);

            $processedResponse = $this->responseHandler->handleResponse($response);

            $this->logger->info('Pago completado exitosamente', [
                'payment_id' => $fields['id'],
            ]);

            return $processedResponse;
        } catch (\Throwable $e) {
            $this->logger->logError($e);

            throw $e;
        }
    }

    /**
     * Información/Consulta de Pago
     * Este método funciona para consultar el estado de un pago.
     *
     * @throws ValidationException
     * @throws GenericException
     * @throws InstapagoTimeoutException
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     */
    public function query(string $id_pago): array
    {
        $this->logger->info('Consultando pago', ['payment_id' => $id_pago]);

        $validationStrategy = new QueryValidationStrategy();
        $validationStrategy->validate(['id' => $id_pago]);

        $requestData = [
            'KeyID' => $this->keyId,
            'PublicKeyId' => $this->publicKeyId,
            'id' => $id_pago,
        ];

        $this->logger->logRequest('GET', 'payment', $requestData);

        try {
            $response = $this->httpClient->request('GET', 'payment', $requestData);
            $this->logger->logResponse($response);

            $processedResponse = $this->responseHandler->handleResponse($response);

            $this->logger->info('Consulta de pago exitosa', [
                'payment_id' => $id_pago,
                'status' => $processedResponse['message'] ?? 'N/A',
            ]);

            return $processedResponse;
        } catch (\Throwable $e) {
            $this->logger->logError($e);

            throw $e;
        }
    }

    /**
     * Cancelar Pago
     * Este método funciona para cancelar un pago previamente procesado.
     *
     * @throws ValidationException
     * @throws GenericException
     * @throws InstapagoTimeoutException
     */
    public function cancel(string $id_pago): array
    {
        $this->logger->info('Cancelando pago', ['payment_id' => $id_pago]);

        $validationStrategy = new QueryValidationStrategy();
        $validationStrategy->validate(['id' => $id_pago]);

        $requestData = [
            'KeyID' => $this->keyId,
            'PublicKeyId' => $this->publicKeyId,
            'id' => $id_pago,
        ];

        $this->logger->logRequest('DELETE', 'payment', $requestData);

        try {
            $response = $this->httpClient->request('DELETE', 'payment', $requestData);
            $this->logger->logResponse($response);

            $this->logger->info('Pago cancelado exitosamente', [
                'payment_id' => $id_pago,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $this->logger->logError($e);

            throw $e;
        }
    }

    /**
     * Procesar un pago.
     *
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws ValidationException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     * @throws InstapagoTimeoutException
     */
    private function processPayment(PaymentType $type, array $fields): array
    {
        $this->logger->info('Iniciando procesamiento de pago', [
            'type' => $type->value,
            'amount' => $fields['amount'] ?? 'N/A',
        ]);

        $validationStrategy = new PaymentValidationStrategy();
        $validationStrategy->validate($fields);

        $requestData = [
            'KeyID' => $this->keyId,
            'PublicKeyId' => $this->publicKeyId,
            'amount' => $fields['amount'],
            'description' => $fields['description'],
            'cardHolder' => $fields['card_holder'],
            'cardHolderId' => $fields['card_holder_id'],
            'cardNumber' => $fields['card_number'],
            'cvc' => $fields['cvc'],
            'expirationDate' => $fields['expiration'],
            'statusId' => $type->value,
            'IP' => $fields['ip'],
        ];

        $this->logger->logRequest('POST', 'payment', $requestData);

        try {
            $response = $this->httpClient->request('POST', 'payment', $requestData);
            $this->logger->logResponse($response);

            $processedResponse = $this->responseHandler->handleResponse($response);

            $this->logger->info('Pago procesado exitosamente', [
                'payment_id' => $processedResponse['id_pago'] ?? 'N/A',
            ]);

            return $processedResponse;
        } catch (\Throwable $e) {
            $this->logger->logError($e);

            throw $e;
        }
    }
}
