<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Assets;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody as HasJsonBodyTrait;

final class BulkUpdateAssetsRequest extends Request implements HasBody
{
    use HasJsonBodyTrait;

    protected Method $method = Method::PATCH;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(protected array $data) {}

    public function resolveEndpoint(): string
    {
        return '/assets';
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultBody(): array
    {
        return $this->data;
    }
}
