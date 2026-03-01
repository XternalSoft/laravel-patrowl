<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;

final class AddTagToAssetRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    public function __construct(
        protected int $assetId,
        protected AddTagToAssetData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return "/assets/{$this->assetId}/tags/add";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        $body = $this->data->toArray();

        if (! isset($body['organization']) && $this->orgId) {
            $body['organization'] = $this->orgId;
        }

        return $body;
    }
}
