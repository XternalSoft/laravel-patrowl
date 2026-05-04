<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum TypeEf5Enum: string
{
    case IP = 'ip';
    case IP_RANGE = 'ip-range';
    case IP_SUBNET = 'ip-subnet';
    case FQDN = 'fqdn';
    case DOMAIN = 'domain';
    case KEYWORD = 'keyword';
    case OTHER = 'other';
}
