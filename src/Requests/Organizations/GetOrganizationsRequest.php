<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Organizations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Data\OrganizationData;

final class GetOrganizationsRequest extends Request implements MapPaginatedResponseItems, Paginatable
{
    protected Method $method = Method::GET;

    /**
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(
        protected array $queryParams = [],
        protected int $limit = 100
    ) {}

    public function resolveEndpoint(): string
    {
        return '/orgs/';
    }

    /**
     * @return array<int, OrganizationData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map(fn (array $item) => OrganizationData::fromApi($item), $response->json('results', []));
    }

    /**
     * @return array<int, OrganizationData>
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

        $params['limit'] = $params['limit'] ?? $this->limit;

        return $params;
    }
}
