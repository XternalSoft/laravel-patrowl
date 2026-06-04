<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum ControlResultEnum: int
{
    case InProgress = 0;
    case NotImpacted = 1;
    case Impacted = 2;
    case PotentiallyImpacted = 3;
}
