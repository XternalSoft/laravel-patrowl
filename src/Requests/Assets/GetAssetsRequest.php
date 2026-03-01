<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Data\AssetInListData;

final class GetAssetsRequest extends Request implements MapPaginatedResponseItems, Paginatable
{
    protected Method $method = Method::GET;

    /**
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(
        protected array $queryParams = [],
        protected ?int $orgId = null,
        protected int $limit = 100
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/';
    }

    /**
     * @return array<int, AssetInListData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map(fn (array $item) => AssetInListData::fromApi($item), $response->json('results', []));
    }

    /**
     * @return array<int, AssetInListData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        return $this->mapPaginatedResponseItems($response);
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
}
