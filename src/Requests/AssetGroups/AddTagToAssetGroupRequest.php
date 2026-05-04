<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetGroups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;

final class AddTagToAssetGroupRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    public function __construct(
        protected int $groupId,
        protected AddTagToAssetData $data,
        protected ?int $orgId = null
    ) {}

    public function resolveEndpoint(): string
    {
        return "/assets/group/{$this->groupId}/tag";
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
