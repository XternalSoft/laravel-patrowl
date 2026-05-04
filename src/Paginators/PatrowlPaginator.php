<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Paginators;

use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\PaginationPlugin\Contracts\MapPaginatedResponseItems;
use Saloon\PaginationPlugin\PagedPaginator;

final class PatrowlPaginator extends PagedPaginator
{
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

    protected function getTotalPages(Response $response): int
    {
        // Patrowl n'envoie pas le nombre total de pages dans le corps de la réponse.
        // Comme nous utilisons isLastPage(), nous pouvons retourner 0 ici car Saloon continuera
        // tant que isLastPage() retourne false.
        return 0;
    }

    protected function getTotalItems(Response $response): int
    {
        return (int) $response->json('count', 0);
    }
}
