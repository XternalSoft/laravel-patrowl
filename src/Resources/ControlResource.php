<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\ControlData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Controls\GetControlRequest;
use Xternalsoft\LaravelPatrowl\Requests\Controls\GetControlsRequest;

final readonly class ControlResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all controls with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#operation/controls_list
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetControlsRequest(
            $queryParams,
            $this->connector->getLimit()
        ));
    }

    /**
     * Get a specific control in detail.
     *
     * @see https://developer.patrowl.io/#operation/controls_read
     */
    public function get(int $id): ControlData
    {
        return $this->connector->send(new GetControlRequest($id))->dtoOrFail();
    }
}
