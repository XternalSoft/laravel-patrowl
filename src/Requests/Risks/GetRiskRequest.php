<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Risks;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Xternalsoft\LaravelPatrowl\Data\RiskData;

final class GetRiskRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected int $id) {}

    public function resolveEndpoint(): string
    {
        return "/risks/{$this->id}/";
    }

    public function createDtoFromResponse(Response $response): RiskData
    {
        return RiskData::fromApi($response->json());
    }
}
