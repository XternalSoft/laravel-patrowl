<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetGroups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class CreateAssetGroupRequest extends Request implements HasBody
{
    use CreatesDtoFromResponse;
    use HasJsonBodyTrait;
    use HasOrganizationContext;

    protected Method $method = Method::POST;

    public function __construct(
        protected CreateAssetGroupData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/group/';
    }

    public function createDtoFromResponse(Response $response): AssetGroupData
    {
        return AssetGroupData::fromApi($response->json());
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->mergeOrganizationId($this->data->toArray(), $this->orgId, 'organization');
    }
}
