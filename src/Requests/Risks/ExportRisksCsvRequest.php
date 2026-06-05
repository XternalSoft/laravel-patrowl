<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Risks;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class ExportRisksCsvRequest extends Request
{
    use HasOrganizationContext;

    protected Method $method = Method::GET;

    /**
     * @param  array<string, mixed>  $queryParams
     */
    public function __construct(
        protected array $queryParams = [],
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/risks/export/csv/';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultQuery(): array
    {
        return $this->mergeOrganizationId($this->queryParams, $this->orgId, 'org_id');
    }
}
