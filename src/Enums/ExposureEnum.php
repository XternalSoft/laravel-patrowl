<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum ExposureEnum: string
{
    case UNKNOWN = 'unknown';
    case EXTERNAL = 'external';
    case INTERNAL = 'internal';
    case RESTRICTED = 'restricted';
}
