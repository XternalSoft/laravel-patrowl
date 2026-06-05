<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetTags;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;
use Xternalsoft\LaravelPatrowl\Requests\Concerns\HasOrganizationContext;

final class BulkAssociateTagsRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;
    use HasOrganizationContext;

    protected Method $method = Method::POST;

    public function __construct(
        protected BulkAssociateTagsData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return '/assets/tags/';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->mergeOrganizationId($this->data->toArray(), $this->orgId, 'organization_id');
    }
}
