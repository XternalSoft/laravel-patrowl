<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetProductsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetTechnologyAggregatesRequest;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetVendorsRequest;

final readonly class TechnologyResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all products with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     */
    public function products(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetProductsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Get all vendors with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     */
    public function vendors(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetVendorsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Retrieve aggregates for a list of Technologies.
     *
     * @param  array<int, int>  $ids
     * @param  array<string, mixed>  $queryParams
     * @return array<int, \Xternalsoft\LaravelPatrowl\Data\TechnologyAggregateData>
     */
    public function aggregates(array $ids, array $queryParams = []): array
    {
        return $this->connector->send(new GetTechnologyAggregatesRequest(
            $ids,
            $queryParams,
            $this->connector->getDefaultOrganizationId()
        ))->throw()->dto();
    }
}
