<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\OrganizationData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetDefaultOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationsRequest;

final readonly class OrganizationResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all organizations with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#operation/orgs_list
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetOrganizationsRequest(
            $queryParams,
            $this->connector->getLimit()
        ));
    }

    /**
     * Get a specific organization's details.
     *
     * @see https://developer.patrowl.io/#operation/orgs_retrieve
     */
    public function get(int $id): OrganizationData
    {
        return $this->connector->send(new GetOrganizationRequest($id))->dtoOrFail();
    }

    /**
     * Get the default organization's details.
     *
     * @see https://developer.patrowl.io/#operation/orgs_default_retrieve
     */
    public function default(): OrganizationData
    {
        return $this->connector->send(new GetDefaultOrganizationRequest())->dtoOrFail();
    }
}
