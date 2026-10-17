<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Paginators;

use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\CursorPaginator;

/**
 * Cursor based paginator: Patrowl returns the next page as a full URL holding a `cursor` query parameter.
 */
final class PatrowlCursorPaginator extends CursorPaginator
{
    protected function getNextCursor(Response $response): string
    {
        parse_str((string) parse_url((string) $response->json('next'), PHP_URL_QUERY), $query);

        return is_string($query['cursor'] ?? null) ? $query['cursor'] : '';
    }

    protected function isLastPage(Response $response): bool
    {
        return $response->json('next') === null;
    }

    /**
     * @return array<int, mixed>
     */
    protected function getPageItems(Response $response, Request $request): array
    {
        if ($request instanceof MapPaginatedResponseItems) {
            return $request->mapPaginatedResponseItems($response);
        }

        return $response->json('results', []);
    }
}
