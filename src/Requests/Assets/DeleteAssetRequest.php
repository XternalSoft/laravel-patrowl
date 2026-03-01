<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteAssetRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(protected int $id) {}

    public function resolveEndpoint(): string
    {
        return "/assets/{$this->id}/";
    }
}
