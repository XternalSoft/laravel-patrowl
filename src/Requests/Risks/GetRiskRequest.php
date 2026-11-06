<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Risks;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Xternalsoft\LaravelPatrowl\Data\RiskData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class GetRiskRequest extends Request
{
    use HasOrganizationContext;

    protected Method $method = Method::GET;

    public function __construct(
        protected int $id,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return "/risks/{$this->id}/";
    }

    public function createDtoFromResponse(Response $response): RiskData
    {
        return RiskData::fromApi($response->json());
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return $this->mergeOrganizationId([], $this->orgId, 'org_id');
    }
}
