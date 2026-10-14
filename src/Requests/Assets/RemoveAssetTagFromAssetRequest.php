<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;

final class RemoveAssetTagFromAssetRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::DELETE;

    public function __construct(protected int $assetId, protected int $tagId) {}

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
            'asset_ids' => [$this->assetId],
            'tag_ids' => [$this->tagId],
        ];
    }
}
