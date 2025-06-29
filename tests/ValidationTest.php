<?php

declare(strict_types=1);

use Instapago\Instapago\Exceptions\ValidationException;
use Instapago\Instapago\Validation\CompletePaymentValidationStrategy;
use Instapago\Instapago\Validation\PaymentValidationStrategy;
use Instapago\Instapago\Validation\QueryValidationStrategy;
use Instapago\Instapago\Validation\ValidationRule;
use Instapago\Instapago\Validation\ValidationRuleBuilder;

describe('Validation System Tests', function () {

    describe('ValidationRule Tests', function () {

        it('validates float values correctly', function () {
            $rule = ValidationRule::float('Amount must be a valid number');

            expect($rule->validate(200.50))->toBeTrue()
                ->and($rule->validate(200))->toBeTrue()
                ->and($rule->validate('200.50'))->toBeTrue()
                ->and($rule->validate('invalid'))->toBeFalse()
                ->and($rule->validate(''))->toBeFalse()
                ->and($rule->message)->toBe('Amount must be a valid number');
        });

        it('validates regex patterns correctly', function () {
            $rule = ValidationRule::regex('/^[0-9]{16}$/', 'Card number must be 16 digits');

            expect($rule->validate('4111111111111111'))->toBeTrue()
                ->and($rule->validate('411111111111111'))->toBeFalse() // 15 digits
                ->and($rule->validate('41111111111111111'))->toBeFalse() // 17 digits
                ->and($rule->validate('411111111111111a'))->toBeFalse() // contains letter
                ->and($rule->message)->toBe('Card number must be 16 digits');
        });

        it('validates IP addresses correctly', function () {
            $rule = ValidationRule::ip('Must be a valid IP address');

            expect($rule->validate('127.0.0.1'))->toBeTrue()
                ->and($rule->validate('192.168.1.1'))->toBeTrue()
                ->and($rule->validate('::1'))->toBeTrue() // IPv6
                ->and($rule->validate('invalid-ip'))->toBeFalse()
                ->and($rule->validate('999.999.999.999'))->toBeFalse()
                ->and($rule->message)->toBe('Must be a valid IP address');
        });

        it('validates integer values correctly', function () {
            $rule = ValidationRule::int('Must be a valid integer');

            expect($rule->validate(123))->toBeTrue()
                ->and($rule->validate('123'))->toBeTrue()
                ->and($rule->validate('123.45'))->toBeFalse()
                ->and($rule->validate('abc'))->toBeFalse()
                ->and($rule->message)->toBe('Must be a valid integer');
        });
    });

    describe('ValidationRuleBuilder Tests', function () {
        it('builds payment validation rules correctly', function () {
            $rules = ValidationRuleBuilder::forPayment();

            expect($rules)->toBeArray()
                ->and($rules)->toHaveCount(8)
                ->and($rules)->toHaveKeys([
                    'amount', 'description', 'card_holder', 'card_holder_id',
                    'card_number', 'cvc', 'expiration', 'ip',
                ])
                ->and($rules['amount'])->toBeInstanceOf(ValidationRule::class)
                ->and($rules['card_number'])->toBeInstanceOf(ValidationRule::class)
                ->and($rules['ip'])->toBeInstanceOf(ValidationRule::class);
        });

        it('builds query validation rules correctly', function () {
            $rules = ValidationRuleBuilder::forQuery();

            expect($rules)->toBeArray()
                ->and($rules)->toHaveCount(1)
                ->and($rules)->toHaveKey('id')
                ->and($rules['id'])->toBeInstanceOf(ValidationRule::class);
        });

        it('builds complete payment validation rules correctly', function () {
            $rules = ValidationRuleBuilder::forComplete();

            expect($rules)->toBeArray()
                ->and($rules)->toHaveCount(2)
                ->and($rules)->toHaveKeys(['id', 'amount'])
                ->and($rules['id'])->toBeInstanceOf(ValidationRule::class)
                ->and($rules['amount'])->toBeInstanceOf(ValidationRule::class);
        });

        it('can build custom rules with fluent interface', function () {
            $builder = new ValidationRuleBuilder();
            $rules = $builder
                ->amount()
                ->cardNumber()
                ->ip()
                ->build();

            expect($rules)->toBeArray()
                ->and($rules)->toHaveCount(3)
                ->and($rules)->toHaveKeys(['amount', 'card_number', 'ip']);
        });
    });

    describe('Validation Strategies Tests', function () {
        it('PaymentValidationStrategy validates correct data', function () {
            $strategy = new PaymentValidationStrategy();
            $validData = [
                'amount' => 200.50,
                'description' => 'Test payment description',
                'card_holder' => 'Juan Pérez',
                'card_holder_id' => '12345678',
                'card_number' => '4111111111111111',
                'cvc' => '123',
                'expiration' => '12/2026',
                'ip' => '127.0.0.1',
            ];

            expect(fn () => $strategy->validate($validData))->not->toThrow(Exception::class);
        });

        it('PaymentValidationStrategy throws ValidationException for invalid amount', function () {
            $strategy = new PaymentValidationStrategy();
            $invalidData = [
                'amount' => 'invalid-amount',
                'description' => 'Test payment',
                'card_holder' => 'Juan Pérez',
                'card_holder_id' => '12345678',
                'card_number' => '4111111111111111',
                'cvc' => '123',
                'expiration' => '12/2026',
                'ip' => '127.0.0.1',
            ];

            expect(fn () => $strategy->validate($invalidData))
                ->toThrow(ValidationException::class);
        });

        it('PaymentValidationStrategy throws ValidationException for invalid card number', function () {
            $strategy = new PaymentValidationStrategy();
            $invalidData = [
                'amount' => 200.50,
                'description' => 'Test payment',
                'card_holder' => 'Juan Pérez',
                'card_holder_id' => '12345678',
                'card_number' => '411111111111111', // 15 digits instead of 16
                'cvc' => '123',
                'expiration' => '12/2026',
                'ip' => '127.0.0.1',
            ];

            expect(fn () => $strategy->validate($invalidData))
                ->toThrow(ValidationException::class);
        });

        it('PaymentValidationStrategy throws ValidationException for invalid IP', function () {
            $strategy = new PaymentValidationStrategy();
            $invalidData = [
                'amount' => 200.50,
                'description' => 'Test payment',
                'card_holder' => 'Juan Pérez',
                'card_holder_id' => '12345678',
                'card_number' => '4111111111111111',
                'cvc' => '123',
                'expiration' => '12/2026',
                'ip' => 'invalid-ip',
            ];

            expect(fn () => $strategy->validate($invalidData))
                ->toThrow(ValidationException::class);
        });

        it('QueryValidationStrategy validates UUID correctly', function () {
            $strategy = new QueryValidationStrategy();
            $validData = [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
            ];

            expect(fn () => $strategy->validate($validData))->not->toThrow(Exception::class);
        });

        it('QueryValidationStrategy throws ValidationException for invalid UUID', function () {
            $strategy = new QueryValidationStrategy();
            $invalidData = [
                'id' => 'invalid-uuid',
            ];

            expect(fn () => $strategy->validate($invalidData))
                ->toThrow(ValidationException::class);
        });

        it('CompletePaymentValidationStrategy validates correctly', function () {
            $strategy = new CompletePaymentValidationStrategy();
            $validData = [
                'id' => '550e8400-e29b-41d4-a716-446655440000',
                'amount' => 200.50,
            ];

            expect(fn () => $strategy->validate($validData))->not->toThrow(Exception::class);
        });

        it('CompletePaymentValidationStrategy throws ValidationException for missing data', function () {
            $strategy = new CompletePaymentValidationStrategy();
            $invalidData = [
                'id' => 'invalid-uuid',
                'amount' => 'invalid-amount',
            ];

            expect(fn () => $strategy->validate($invalidData))
                ->toThrow(ValidationException::class);
        });
    });

    describe('Custom Validation Messages Tests', function () {
        it('returns custom error messages in Spanish', function () {
            $strategy = new PaymentValidationStrategy();
            $invalidData = [
                'amount' => 'invalid',
                'description' => str_repeat('a', 200), // Too long
                'card_holder' => '123', // Numbers not allowed
                'card_holder_id' => '123', // Too short
                'card_number' => '123', // Too short
                'cvc' => 'abc', // Not a number
                'expiration' => 'invalid', // Wrong format
                'ip' => 'invalid', // Invalid IP
            ];

            try {
                $strategy->validate($invalidData);
                expect(false)->toBeTrue('Should have thrown ValidationException');
            } catch (ValidationException $e) {
                $errors = json_decode($e->getMessage(), true);

                expect($errors)->toBeArray()
                    ->and($errors)->toHaveKeys([
                        'amount', 'description', 'card_holder', 'card_holder_id',
                        'card_number', 'cvc', 'expiration', 'ip',
                    ])
                    ->and($errors['amount'])->toContain('número decimal válido')
                    ->and($errors['description'])->toContain('140 caracteres')
                    ->and($errors['card_holder'])->toContain('letras y espacios')
                    ->and($errors['card_holder_id'])->toContain('5 y 8 dígitos')
                    ->and($errors['card_number'])->toContain('16 dígitos')
                    ->and($errors['cvc'])->toContain('número válido')
                    ->and($errors['expiration'])->toContain('MM/YYYY')
                    ->and($errors['ip'])->toContain('dirección IP válida');
            }
        });
    });
});
