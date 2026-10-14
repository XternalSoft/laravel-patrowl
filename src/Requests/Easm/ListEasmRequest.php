<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Easm;

use Closure;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\Contracts\Paginatable;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

/**
 * Lists any EASM resource (domains, ipv4s, services, ...): they all share the same list contract.
 *
 * @template TData of object
 */
final class ListEasmRequest extends Request implements MapPaginatedResponseItems, Paginatable
{
    use HasOrganizationContext;

    protected Method $method = Method::GET;

    /**
     * @param  Closure(array<string, mixed>): TData  $mapper
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(
        protected string $endpoint,
        protected Closure $mapper,
        protected array $queryParams = [],
        protected ?int $orgId = null,
        protected int $limit = 100
    ) {}

    public function resolveEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return array<int, TData>
     */
    public function mapPaginatedResponseItems(Response $response): array
    {
        return array_map($this->mapper, $response->json('results', []));
    }

    /**
     * @return array<int, TData>
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
