<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Enums;

enum ControlStatusEnum: int
{
    case InProgress = 0;
    case Finished = 1;
}
