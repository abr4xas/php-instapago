<?php

declare(strict_types=1);

namespace Instapago\Instapago\DTOs;

final readonly class CompletePaymentRequest
{
    public function __construct(
        public string $id,
        public float $amount
    ) {}

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            amount: (float) $data['amount']
        );
    }
}
