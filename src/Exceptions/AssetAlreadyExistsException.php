<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Exceptions;

use Exception;
use Saloon\Http\Response;

final class AssetAlreadyExistsException extends Exception
{
    public function __construct(public readonly Response $response)
    {
        parent::__construct('The asset already exists.');
    }
}
