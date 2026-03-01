<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetTags;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;

final class GetAssetTagRequest extends Request
{
    use CreatesDtoFromResponse;

    protected Method $method = Method::GET;

    public function __construct(protected int $id) {}

    public function resolveEndpoint(): string
    {
        return "/assets/tags/{$this->id}/";
    }

    public function createDtoFromResponse(Response $response): AssetTagData
    {
        return AssetTagData::fromApi($response->json());
    }
}
