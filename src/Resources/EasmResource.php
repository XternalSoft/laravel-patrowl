<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Closure;
use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Paginators\PatrowlCursorPaginator;
use Xternalsoft\LaravelPatrowl\Requests\Easm\GetEasmRequest;
use Xternalsoft\LaravelPatrowl\Requests\Easm\ListEasmRequest;

/**
 * Read access to an EASM resource (domains, ipv4s, services, CVEs, ...).
 *
 * @template TData of object
 */
final readonly class EasmResource
{
    /**
     * @param  Closure(array<string, mixed>): TData  $mapper
     */
    public function __construct(
        private LaravelPatrowl $connector,
        private string $endpoint,
        private Closure $mapper,
        private bool $usesCursorPagination = false
    ) {}

    /**
     * Get all items with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     */
    public function all(array $queryParams = []): Paginator
    {
        $request = new ListEasmRequest(
            $this->endpoint,
            $this->mapper,
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        );

        return $this->usesCursorPagination
            ? new PatrowlCursorPaginator($this->connector, $request)
            : $this->connector->paginate($request);
    }

    /**
     * Get a single item by its ID (or identifier for CVEs, e.g. `CVE-2026-12345`).
     *
     * @return TData
     */
    public function get(int|string $id): object
    {
        return $this->connector->send(new GetEasmRequest($this->endpoint, $id, $this->mapper))->dtoOrFail();
    }
}
