<?php

declare(strict_types=1);

arch('it will not use debugging functions')
    ->expect(['dd', 'dump', 'ray'])
    ->each->not->toBeUsed();

arch('concerns directory must only contain traits')
    ->expect('Xternalsoft\LaravelPatrowl\Requests\Concerns')
    ->toBeTraits();

arch('requests must be final and extend Saloon Request')
    ->expect('Xternalsoft\LaravelPatrowl\Requests')
    ->classes()
    ->toBeFinal()
    ->toExtend('Saloon\Http\Request')
    ->ignoring('Xternalsoft\LaravelPatrowl\Requests\Concerns');

arch('enums directory must only contain enums')
    ->expect('Xternalsoft\LaravelPatrowl\Enums')
    ->toBeEnums();
