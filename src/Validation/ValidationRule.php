<?php

declare(strict_types=1);

namespace Instapago\Instapago\Validation;

final class ValidationRule
{
    public function __construct(
        public int $filter,
        public array $options = [],
        public string $message = 'Invalid value'
    ) {}

    public function validate(mixed $value): bool
    {
        if ($this->filter === FILTER_VALIDATE_REGEXP) {
            $options = ['options' => ['regexp' => $this->options['regexp'] ?? '']];
        } else {
            $options = $this->options;
        }

        return filter_var($value, $this->filter, $options) !== false;
    }

    public static function float(string $message = 'Debe ser un número decimal válido'): self
    {
        return new self(FILTER_VALIDATE_FLOAT, [], $message);
    }

    public static function regex(string $pattern, string $message = 'Formato inválido'): self
    {
        return new self(FILTER_VALIDATE_REGEXP, ['regexp' => $pattern], $message);
    }

    public static function ip(string $message = 'Debe ser una dirección IP válida'): self
    {
        return new self(FILTER_VALIDATE_IP, [], $message);
    }

    public static function int(string $message = 'Debe ser un número entero válido'): self
    {
        return new self(FILTER_VALIDATE_INT, [], $message);
    }
}
