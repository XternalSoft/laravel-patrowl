<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Technologies;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Data\VendorData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class GetVendorsRequest extends Request implements MapPaginatedResponseItems, Paginatable
{
    use HasOrganizationContext;

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
        return '/asm/vendor/';
    }

    /**
     * @return array<int, VendorData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map(fn (array $item) => VendorData::fromApi($item), $response->json('results', []));
    }

    /**
     * @return array<int, VendorData>
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
        $params = $this->mergeOrganizationId($this->queryParams, $this->orgId, 'org_id');

        $params['limit'] = $params['limit'] ?? $this->limit;

        return $params;
    }
}
