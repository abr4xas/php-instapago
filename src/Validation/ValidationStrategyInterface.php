<?php

declare(strict_types=1);

namespace Instapago\Instapago\Validation;

interface ValidationStrategyInterface
{
    public function validate(array $data): void;

    public function getRules(): array;
}
