<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Requests\Organizations;

use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Request\CreatesDtoFromResponse;
use Xternalsoft\LaravelPatrowl\Data\OrganizationData;

final class GetDefaultOrganizationRequest extends Request
{
    use CreatesDtoFromResponse;

    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/orgs/default/';
    }

    public function createDtoFromResponse(Response $response): OrganizationData
    {
        return OrganizationData::fromApi($response->json());
    }
}
