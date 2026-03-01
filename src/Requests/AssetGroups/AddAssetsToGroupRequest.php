<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\AssetGroups;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;

final class AddAssetsToGroupRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::POST;

    /**
     * @param array<int, int> $assetIds
     */
    public function __construct(protected int $groupId, protected array $assetIds) {}

    public function resolveEndpoint(): string
    {
        return "/assets/group/{$this->groupId}/assets";
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return ['assets_id' => $this->assetIds];
    }
}
