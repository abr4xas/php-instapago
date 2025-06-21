<?php

declare(strict_types=1);

/**
 * The MIT License (MIT)
 * Copyright (c) 2016 Angel Cruz <me@abr4xas.org>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the “Software”), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author José Gómez <1josegomezr@gmail.com>
 * @license MIT License
 * @copyright 2016 José Gómez
 */

namespace Instapago\Instapago;

use Instapago\Instapago\Exceptions\ValidationException;

final class Validator
{
    private array $validations = [];

    public function setValidations(string $type): self
    {
        $rules = $this->getValidationRules();
        $this->validations = $rules[$type] ?? [];

        return $this;
    }

    /**
     * @throws ValidationException
     */
    public function validate(array $fields): void
    {
        $errors = [];
        foreach ($this->validations as $key => $filters) {
            if (! $this->_validation($fields[$key] ?? null, $filters)) {
                $errors[$key] = "Invalid value for {$key}";
            }
        }

        if ($errors) {
            throw new ValidationException(json_encode($errors));
        }
    }

    private function _validation(mixed $value, array $filters): bool
    {
        $filter = $filters[0];
        $options = $filter === FILTER_VALIDATE_REGEXP ? ['options' => ['regexp' => $filters[1]]] : [];

        return filter_var($value, $filter, $options) !== false;
    }

    private function getValidationRules(): array
    {
        return [
            'payment' => [
                'amount' => [FILTER_VALIDATE_FLOAT],
                'description' => [FILTER_VALIDATE_REGEXP, '/^(.{0,140})$/'],
                'card_holder' => [FILTER_VALIDATE_REGEXP, '/^([a-zA-ZáéíóúñÁÉÍÓÚÑ\ ]+)$/'],
                'card_holder_id' => [FILTER_VALIDATE_REGEXP, '/^(\d{5,8})$/'],
                'card_number' => [FILTER_VALIDATE_REGEXP, '/^(\d{16})$/'],
                'cvc' => [FILTER_VALIDATE_INT],
                'expiration' => [FILTER_VALIDATE_REGEXP, '/^(\d{2})\/(\d{4})$/'],
                'ip' => [FILTER_VALIDATE_IP],
            ],
            'release' => [
                'amount' => [FILTER_VALIDATE_FLOAT],
                'id' => [FILTER_VALIDATE_REGEXP, '/^([0-9a-f]{8})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{12})$/'],
            ],
            'query' => [
                'id' => [FILTER_VALIDATE_REGEXP, '/^([0-9a-f]{8})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{4})\-([0-9a-f]{12})$/'],
            ],
        ];
    }
}
