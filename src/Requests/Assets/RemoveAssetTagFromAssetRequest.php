<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Enums\Method;
use Saloon\Http\Request;

final class RemoveAssetTagFromAssetRequest extends Request
{
    protected Method $method = Method::DELETE;

    public function __construct(protected int $assetId, protected int $tagId) {}

    public function resolveEndpoint(): string
    {
        return "/assets/{$this->assetId}/tags/{$this->tagId}/";
    }
}
