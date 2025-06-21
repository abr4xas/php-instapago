<?php

declare(strict_types=1);

namespace Instapago\Instapago\DTOs;

final readonly class PaymentRequest
{
    public function __construct(
        public float $amount,
        public string $description,
        public string $cardHolder,
        public string $cardHolderId,
        public string $cardNumber,
        public string $cvc,
        public string $expiration,
        public string $ip
    ) {}

    public function toArray(): array
    {
        return [
            'amount' => $this->amount,
            'description' => $this->description,
            'card_holder' => $this->cardHolder,
            'card_holder_id' => $this->cardHolderId,
            'card_number' => $this->cardNumber,
            'cvc' => $this->cvc,
            'expiration' => $this->expiration,
            'ip' => $this->ip,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            amount: (float) $data['amount'],
            description: $data['description'],
            cardHolder: $data['card_holder'],
            cardHolderId: $data['card_holder_id'],
            cardNumber: $data['card_number'],
            cvc: $data['cvc'],
            expiration: $data['expiration'],
            ip: $data['ip']
        );
    }
}
