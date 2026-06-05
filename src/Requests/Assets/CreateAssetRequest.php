<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class CreateAssetRequest extends Request implements HasBody
{
    use CreatesDtoFromResponse;
    use HasJsonBodyTrait;
    use HasOrganizationContext;

    protected Method $method = Method::POST;

    public function __construct(
        protected CreateAssetData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/';
    }

    public function createDtoFromResponse(Response $response): AssetData
    {
        return AssetData::fromApi($response->json());
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->mergeOrganizationId($this->data->toArray(), $this->orgId, 'org_id');
    }
}
