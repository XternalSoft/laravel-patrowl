<?php

namespace Xternalsoft\LaravelPatrowl\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Xternalsoft\LaravelPatrowl\LaravelPatrowl
 */
class LaravelPatrowl extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Xternalsoft\LaravelPatrowl\LaravelPatrowl::class;
    }
}
