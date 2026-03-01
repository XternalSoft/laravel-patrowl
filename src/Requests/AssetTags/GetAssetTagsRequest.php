<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetTags;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;

final class GetAssetTagsRequest extends Request implements Paginatable, MapPaginatedResponseItems
{
    protected Method $method = Method::GET;

    /**
     * @param array<string, mixed> $queryParams
     */
    public function __construct(
        protected array $queryParams = [],
        protected ?int $orgId = null,
        protected int $limit = 100
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/tags/';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        $params = $this->queryParams;

        if (! isset($params['org_id']) && ! isset($params['organization']) && $this->orgId) {
            $params['org_id'] = $this->orgId;
        }

        $params['limit'] = $params['limit'] ?? $this->limit;

        return $params;
    }

    /**
     * @return array<int, AssetTagData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map(fn (array $item) => AssetTagData::fromApi($item), $response->json('results', []));
    }

    /**
     * @return array<int, AssetTagData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        return $this->mapPaginatedResponseItems($response);
    }
}
