<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Controls;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Data\ControlLiteData;

final class GetControlsRequest extends Request implements MapPaginatedResponseItems, Paginatable
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
        return '/controls/';
    }

    /**
     * @return array<int, ControlLiteData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map(fn (array $item) => ControlLiteData::fromApi($item), $response->json('results', []));
    }

    /**
     * @return array<int, ControlLiteData>
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
