<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;

final class SyncAssetTagsRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    /**
     * @param array<int, int> $tagIds
     */
    public function __construct(protected int $assetId, protected array $tagIds) {}

    public function resolveEndpoint(): string
    {
        return "/assets/{$this->assetId}/tags";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return ['tags' => $this->tagIds];
    }
}
