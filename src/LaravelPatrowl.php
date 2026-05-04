<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl;

use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Request;
use Saloon\PaginationPlugin\Contracts\HasPagination;
use Saloon\PaginationPlugin\Paginator;
use Saloon\Traits\HasMockClient;
use Saloon\Traits\Plugins\AcceptsJson;
use Xternalsoft\LaravelPatrowl\Paginators\PatrowlPaginator;
use Xternalsoft\LaravelPatrowl\Resources\AssetGroupResource;
use Xternalsoft\LaravelPatrowl\Resources\AssetResource;
use Xternalsoft\LaravelPatrowl\Resources\AssetTagResource;

final class LaravelPatrowl extends Connector implements HasPagination
{
    use AcceptsJson;
    use HasMockClient;

    public function __construct(
        private readonly ?string $apiToken,
        private readonly string $baseUrl,
        private readonly ?int $defaultOrganizationId = null,
        private readonly int $limit = 100,
        private readonly int $timeout = 30
    ) {
        if (! $this->apiToken) {
            throw new Exceptions\MissingApiTokenException('Patrowl API token is not configured.');
        }
    }

    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function paginate(Request $request): Paginator
    {
        return new PatrowlPaginator($this, $request);
    }

    public function assets(): AssetResource
    {
        return new AssetResource($this);
    }

    public function assetGroups(): AssetGroupResource
    {
        return new AssetGroupResource($this);
    }

    public function assetTags(): AssetTagResource
    {
        return new AssetTagResource($this);
    }

    public function getDefaultOrganizationId(): ?int
    {
        return $this->defaultOrganizationId;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->apiToken, prefix: 'Token');
    }

    /**
     * @return array<string, mixed>
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => $this->timeout,
        ];
    }
}
