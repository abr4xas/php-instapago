<?php

declare(strict_types=1);

namespace Instapago\Instapago\Validation;

final class ValidationRuleBuilder
{
    private array $rules = [];

    public function amount(): self
    {
        $this->rules['amount'] = ValidationRule::float('El monto debe ser un número decimal válido');

        return $this;
    }

    public function description(): self
    {
        $this->rules['description'] = ValidationRule::regex(
            '/^(.{0,140})$/',
            'La descripción no puede exceder 140 caracteres'
        );

        return $this;
    }

    public function cardHolder(): self
    {
        $this->rules['card_holder'] = ValidationRule::regex(
            '/^([a-zA-ZáéíóúñÁÉÍÓÚÑ\ ]+)$/',
            'El nombre del titular debe contener solo letras y espacios'
        );

        return $this;
    }

    public function cardHolderId(): self
    {
        $this->rules['card_holder_id'] = ValidationRule::regex(
            '/^(\d{5,8})$/',
            'La cédula debe tener entre 5 y 8 dígitos'
        );

        return $this;
    }

    public function cardNumber(): self
    {
        $this->rules['card_number'] = ValidationRule::regex(
            '/^(\d{16})$/',
            'El número de tarjeta debe tener exactamente 16 dígitos'
        );

        return $this;
    }

    public function cvc(): self
    {
        $this->rules['cvc'] = ValidationRule::int('El CVC debe ser un número válido');

        return $this;
    }

    public function expiration(): self
    {
        $this->rules['expiration'] = ValidationRule::regex(
            '/^(\d{2})\/(\d{4})$/',
            'La fecha de expiración debe tener el formato MM/YYYY'
        );

        return $this;
    }

    public function ip(): self
    {
        $this->rules['ip'] = ValidationRule::ip('Debe ser una dirección IP válida');

        return $this;
    }

    public function paymentId(): self
    {
        $this->rules['id'] = ValidationRule::regex(
            '/^([0-9a-f]{8})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{12})$/',
            'El ID de pago debe ser un UUID válido'
        );

        return $this;
    }

    public function build(): array
    {
        return $this->rules;
    }

    public static function forPayment(): array
    {
        return (new self())
            ->amount()
            ->description()
            ->cardHolder()
            ->cardHolderId()
            ->cardNumber()
            ->cvc()
            ->expiration()
            ->ip()
            ->build();
    }

    public static function forQuery(): array
    {
        return (new self())
            ->paymentId()
            ->build();
    }

    public static function forComplete(): array
    {
        return (new self())
            ->paymentId()
            ->amount()
            ->build();
    }
}
