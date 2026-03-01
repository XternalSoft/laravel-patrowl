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

final class CreateAssetGroupRequest extends Request implements HasBody
{
    use CreatesDtoFromResponse;
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    public function __construct(
        protected CreateAssetGroupData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/group/';
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

    public function createDtoFromResponse(Response $response): AssetGroupData
    {
        return AssetGroupData::fromApi($response->json());
    }
}
