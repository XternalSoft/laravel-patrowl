<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetTags;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetTagData;

final class CreateAssetTagRequest extends Request implements HasBody
{
    use CreatesDtoFromResponse;
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    public function __construct(
        protected CreateAssetTagData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/tags/';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = $this->data->toArray();

        if (! isset($body['organization']) && $this->orgId) {
            $body['organization'] = $this->orgId;
        }

        return $body;
    }

    public function createDtoFromResponse(Response $response): AssetTagData
    {
        return AssetTagData::fromApi($response->json());
    }
}
