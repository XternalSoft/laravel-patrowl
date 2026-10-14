<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Xternalsoft\LaravelPatrowl\Data\OrganizationData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationLiteData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationsRequest;

final readonly class OrganizationResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all organizations.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array<int, OrganizationLiteData>
     *
     * @see https://developer.patrowl.io/#operation/orgs_list
     */
    public function all(array $queryParams = []): array
    {
        return $this->connector->send(new GetOrganizationsRequest($queryParams))->dtoOrFail();
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
}
