<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum LivenessEnum: string
{
    case UNKNOWN = 'unknown';
    case UP = 'up';
    case DOWN = 'down';
}
