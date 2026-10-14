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
use Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException;
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
        if ($response->status() === 208) {
            throw new AssetAlreadyExistsException($response);
        }

        // shortcut: a CIDR value returns {type, asset_group} instead of an asset and is not handled, add support if subnet creation is needed.
        return AssetData::fromApi($response->json('data'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->mergeOrganizationId($this->data->toArray(), $this->orgId, 'org_id');
    }
}
