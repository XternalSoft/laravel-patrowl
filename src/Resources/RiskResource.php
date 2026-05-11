<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\RiskData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Risks\ExportRisksCsvRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRiskRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRisksRequest;

final readonly class RiskResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all risks with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#patrowl-dashboard-api-risks
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetRisksRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Get a specific risk.
     *
     * @see https://developer.patrowl.io/#patrowl-dashboard-api-risks
     */
    public function get(int $id): RiskData
    {
        return $this->connector->send(new GetRiskRequest($id))->dtoOrFail();
    }

    /**
     * Export risks to CSV.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#patrowl-dashboard-api-risks
     */
    public function exportCsv(array $queryParams = []): string
    {
        return $this->connector->send(new ExportRisksCsvRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId()
        ))->body();
    }
}
