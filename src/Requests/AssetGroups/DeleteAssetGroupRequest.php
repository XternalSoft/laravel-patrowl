<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetGroups;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class DeleteAssetGroupRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(protected int $id) {}

    public function resolveEndpoint(): string
    {
        return "/assets/group/{$this->id}/";
    }
}
