<?php

declare(strict_types=1);

namespace Instapago\Instapago\DTOs;

final readonly class PaymentResponse
{
    public function __construct(
        public string $code,
        public string $message,
        public string $voucher = '',
        public string $idPago = '',
        public string $reference = '',
        public array $originalResponse = []
    ) {}

    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'voucher' => $this->voucher,
            'id_pago' => $this->idPago,
            'reference' => $this->reference,
            'original_response' => $this->originalResponse,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            code: $data['code'],
            message: $data['message'],
            voucher: html_entity_decode($data['voucher'] ?? ''),
            idPago: $data['id'] ?? '',
            reference: $data['reference'] ?? '',
            originalResponse: $data
        );
    }

    public function isSuccessful(): bool
    {
        return $this->code === '201';
    }
}
