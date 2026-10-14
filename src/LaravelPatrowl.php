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
use Xternalsoft\LaravelPatrowl\Data\Easm\AsnData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CertificateData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CveData;
use Xternalsoft\LaravelPatrowl\Data\Easm\DomainData;
use Xternalsoft\LaravelPatrowl\Data\Easm\Ipv4Data;
use Xternalsoft\LaravelPatrowl\Data\Easm\ProductData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ResolutionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ServiceData;
use Xternalsoft\LaravelPatrowl\Data\Easm\TechnologyData;
use Xternalsoft\LaravelPatrowl\Data\Easm\TechnologyDetectionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\VendorData;
use Xternalsoft\LaravelPatrowl\Data\Easm\WebServerData;
use Xternalsoft\LaravelPatrowl\Paginators\PatrowlPaginator;
use Xternalsoft\LaravelPatrowl\Resources\AssetGroupResource;
use Xternalsoft\LaravelPatrowl\Resources\AssetResource;
use Xternalsoft\LaravelPatrowl\Resources\AssetTagResource;
use Xternalsoft\LaravelPatrowl\Resources\ControlResource;
use Xternalsoft\LaravelPatrowl\Resources\EasmResource;
use Xternalsoft\LaravelPatrowl\Resources\OrganizationResource;
use Xternalsoft\LaravelPatrowl\Resources\RiskResource;

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

    public function controls(): ControlResource
    {
        return new ControlResource($this);
    }

    public function risks(): RiskResource
    {
        return new RiskResource($this);
    }

    public function organizations(): OrganizationResource
    {
        return new OrganizationResource($this);
    }

    /**
     * Domains (and subdomains) discovered on the attack surface.
     *
     * @return EasmResource<DomainData>
     */
    public function domains(): EasmResource
    {
        return new EasmResource($this, '/domains', DomainData::fromApi(...));
    }

    /**
     * IPv4 addresses, owned or contextualized.
     *
     * @return EasmResource<Ipv4Data>
     */
    public function ipv4s(): EasmResource
    {
        return new EasmResource($this, '/ipv4s', Ipv4Data::fromApi(...));
    }

    /**
     * Domain to IPv4 resolutions seen in the last 90 days.
     *
     * @return EasmResource<ResolutionData>
     */
    public function resolutions(): EasmResource
    {
        return new EasmResource($this, '/resolutions', ResolutionData::fromApi(...));
    }

    /**
     * Autonomous systems of the assets IPv4 addresses.
     *
     * @return EasmResource<AsnData>
     */
    public function asns(): EasmResource
    {
        return new EasmResource($this, '/asns', AsnData::fromApi(...));
    }

    /**
     * Network services (open ports) detected on assets.
     *
     * @return EasmResource<ServiceData>
     */
    public function services(): EasmResource
    {
        return new EasmResource($this, '/services', ServiceData::fromApi(...));
    }

    /**
     * Web servers detected on exposed services.
     *
     * @return EasmResource<WebServerData>
     */
    public function webServers(): EasmResource
    {
        return new EasmResource($this, '/webservers', WebServerData::fromApi(...));
    }

    /**
     * TLS certificates detected on services.
     *
     * @return EasmResource<CertificateData>
     */
    public function certificates(): EasmResource
    {
        return new EasmResource($this, '/certificates', CertificateData::fromApi(...));
    }

    /**
     * Vendors catalog of detected technologies.
     *
     * @return EasmResource<VendorData>
     */
    public function vendors(): EasmResource
    {
        return new EasmResource($this, '/vendors', VendorData::fromApi(...));
    }

    /**
     * Products catalog of detected technologies.
     *
     * @return EasmResource<ProductData>
     */
    public function products(): EasmResource
    {
        return new EasmResource($this, '/products', ProductData::fromApi(...));
    }

    /**
     * Distinct product/version combinations detected on assets.
     *
     * @return EasmResource<TechnologyData>
     */
    public function technologies(): EasmResource
    {
        return new EasmResource($this, '/technologies', TechnologyData::fromApi(...));
    }

    /**
     * Individual technology detections (asset, service, when).
     *
     * @return EasmResource<TechnologyDetectionData>
     */
    public function technologyDetections(): EasmResource
    {
        return new EasmResource($this, '/technology-detections', TechnologyDetectionData::fromApi(...));
    }

    /**
     * CVEs catalog, cursor paginated, retrieved by identifier (e.g. `CVE-2026-12345`).
     *
     * @return EasmResource<CveData>
     */
    public function cves(): EasmResource
    {
        return new EasmResource($this, '/cves', CveData::fromApi(...), usesCursorPagination: true);
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
