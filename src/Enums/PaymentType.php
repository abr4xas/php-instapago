<?php

declare(strict_types=1);

namespace Instapago\Instapago\Enums;

enum PaymentType: string
{
    case RESERVED = '1';
    case DIRECT = '2';
}
