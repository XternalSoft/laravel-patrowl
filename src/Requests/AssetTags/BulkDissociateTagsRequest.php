<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetTags;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;

final class BulkDissociateTagsRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::DELETE;

    /**
     * @param  array<int, int>  $assetIds
     * @param  array<int, int>  $tagIds
     */
    public function __construct(
        protected array $assetIds,
        protected array $tagIds,
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
        return [
            'asset_ids' => $this->assetIds,
            'tag_ids' => $this->tagIds,
        ];
    }
}
