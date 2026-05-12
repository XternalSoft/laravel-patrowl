<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Risks;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class ExportRisksCsvRequest extends Request
{
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
        $params = $this->queryParams;

        if (! isset($params['org_id']) && ! isset($params['organization']) && $this->orgId) {
            $params['org_id'] = $this->orgId;
        }

        return $params;
    }
}
