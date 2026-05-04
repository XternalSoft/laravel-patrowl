<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum AssetOutsideBusinessHoursEnum: int
{
    case UNACTIVATED = 0;
    case ACTIVATED = 1;
}
