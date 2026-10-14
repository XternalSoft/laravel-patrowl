<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Organizations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\OrganizationLiteData;

final class GetOrganizationsRequest extends Request
{
    use CreatesDtoFromResponse;

    protected Method $method = Method::GET;

    /**
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(protected array $queryParams = []) {}

    public function resolveEndpoint(): string
    {
        return '/orgs/';
    }

    /**
     * @return array<int, OrganizationLiteData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        return array_map(fn (array $item) => OrganizationLiteData::fromApi($item), $response->json());
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return $this->queryParams;
    }
}
