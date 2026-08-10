<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Technologies;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Xternalsoft\LaravelPatrowl\Data\TechnologyAggregateData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class GetTechnologyAggregatesRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;
    use HasOrganizationContext;

    protected Method $method = Method::POST;

    /**
     * @param  array<int, int>  $ids
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(
        protected array $ids,
        protected array $queryParams = [],
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/asm/technology/aggregates/';
    }

    /**
     * @return array<int, TechnologyAggregateData>
     */
    public function createDtoFromResponse(Response $response): array
    {
        $counts = $response->json('counts', []);

        return array_map(fn (array $item) => TechnologyAggregateData::fromApi($item), $counts);
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return [
            'ids' => $this->ids,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return $this->mergeOrganizationId($this->queryParams, $this->orgId, 'org_id');
    }
}
