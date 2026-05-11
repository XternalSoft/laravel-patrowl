<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum RiskSeverityEnum: int
{
    case Info = 0;
    case Low = 1;
    case Medium = 2;
    case High = 3;
    case Critical = 4;
}
