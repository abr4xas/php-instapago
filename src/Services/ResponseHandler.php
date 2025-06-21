<?php

declare(strict_types=1);

namespace Instapago\Instapago\Services;

use Instapago\Instapago\Exceptions\GenericException;
use Instapago\Instapago\Exceptions\InstapagoAuthException;
use Instapago\Instapago\Exceptions\InstapagoBankRejectException;
use Instapago\Instapago\Exceptions\InstapagoException;
use Instapago\Instapago\Exceptions\InstapagoInvalidInputException;

final class ResponseHandler
{
    /**
     * @throws InstapagoAuthException
     * @throws InstapagoBankRejectException
     * @throws GenericException
     * @throws InstapagoException
     * @throws InstapagoInvalidInputException
     */
    public function handleResponse(array $response): array
    {
        return match ($response['code']) {
            '400' => throw new InstapagoInvalidInputException('Datos inválidos.'),
            '401' => throw new InstapagoAuthException('Error de autenticación.'),
            '403' => throw new InstapagoBankRejectException('Pago rechazado por el banco.'),
            '500' => throw new InstapagoException('Error interno del servidor.'),
            '503' => throw new InstapagoException('Error al procesar los parámetros de entrada.'),
            '201' => $this->formatSuccessResponse($response),
            default => throw new GenericException('Respuesta no implementada: ' . $response['code']),
        };
    }

    private function formatSuccessResponse(array $response): array
    {
        return [
            'code' => $response['code'],
            'message' => $response['message'],
            'voucher' => html_entity_decode($response['voucher'] ?? ''),
            'id_pago' => $response['id'] ?? '',
            'reference' => $response['reference'] ?? '',
            'original_response' => $response,
        ];
    }
}
