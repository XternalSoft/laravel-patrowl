<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xternalsoft\LaravelPatrowl\LaravelPatrowl
 */
final class LaravelPatrowl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xternalsoft\LaravelPatrowl\LaravelPatrowl::class;
    }
}
