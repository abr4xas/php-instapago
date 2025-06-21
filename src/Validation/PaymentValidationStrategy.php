<?php

declare(strict_types=1);

namespace Instapago\Instapago\Validation;

use Instapago\Instapago\Exceptions\ValidationException;

final class PaymentValidationStrategy implements ValidationStrategyInterface
{
    /**
     * @throws ValidationException
     */
    public function validate(array $data): void
    {
        $rules = $this->getRules();
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            if (! $rule->validate($value)) {
                $errors[$field] = $rule->message;
            }
        }

        if ($errors) {
            throw new ValidationException(json_encode($errors));
        }
    }

    public function getRules(): array
    {
        return ValidationRuleBuilder::forPayment();
    }
}
