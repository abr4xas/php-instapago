<?php

declare(strict_types=1);

namespace Instapago\Instapago;

interface InstapagoApiInterface
{
    public function directPayment(array $fields): array;

    public function reservePayment(array $fields): array;

    public function completePayment(array $fields): array;

    public function query(string $id_pago): array;

    public function cancel(string $id_pago): array;
}
