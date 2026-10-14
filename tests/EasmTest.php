<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Xternalsoft\LaravelPatrowl\Data\Easm\AsnData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CertificateData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CertificateInServiceData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CveData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CveDetailData;
use Xternalsoft\LaravelPatrowl\Data\Easm\CvssData;
use Xternalsoft\LaravelPatrowl\Data\Easm\DnsRecordData;
use Xternalsoft\LaravelPatrowl\Data\Easm\DomainData;
use Xternalsoft\LaravelPatrowl\Data\Easm\DomainInResolutionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ExploitData;
use Xternalsoft\LaravelPatrowl\Data\Easm\Ipv4Data;
use Xternalsoft\LaravelPatrowl\Data\Easm\Ipv4InResolutionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\Ipv4ProviderData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ProductData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ProductInTechnologyData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ResolutionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ServiceData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ServiceDetailData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ServiceInCertificateData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ServiceInTechnologyDetectionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\TechnologyData;
use Xternalsoft\LaravelPatrowl\Data\Easm\TechnologyDetectionData;
use Xternalsoft\LaravelPatrowl\Data\Easm\TechnologyInCveData;
use Xternalsoft\LaravelPatrowl\Data\Easm\ThreatInfoData;
use Xternalsoft\LaravelPatrowl\Data\Easm\VendorData;
use Xternalsoft\LaravelPatrowl\Data\Easm\VendorInProductData;
use Xternalsoft\LaravelPatrowl\Data\Easm\WebServerData;
use Xternalsoft\LaravelPatrowl\Data\Easm\WebServerInServiceData;
use Xternalsoft\LaravelPatrowl\Data\Easm\WebServerServiceData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Easm\GetEasmRequest;
use Xternalsoft\LaravelPatrowl\Requests\Easm\ListEasmRequest;

beforeEach(function () {
    config()->set('patrowl.api_token', 'fake-token');
});

function easmPage(array $results, ?string $next = null): array
{
    return ['count' => count($results), 'next' => $next, 'previous' => null, 'results' => $results];
}

dataset('easm resources', [
    'domains' => ['domains', '/domains', DomainData::class, DomainData::class],
    'ipv4s' => ['ipv4s', '/ipv4s', Ipv4Data::class, Ipv4Data::class],
    'resolutions' => ['resolutions', '/resolutions', ResolutionData::class, ResolutionData::class],
    'asns' => ['asns', '/asns', AsnData::class, AsnData::class],
    'services' => ['services', '/services', ServiceData::class, ServiceDetailData::class],
    'webServers' => ['webServers', '/webservers', WebServerData::class, WebServerData::class],
    'certificates' => ['certificates', '/certificates', CertificateData::class, CertificateData::class],
    'vendors' => ['vendors', '/vendors', VendorData::class, VendorData::class],
    'products' => ['products', '/products', ProductData::class, ProductData::class],
    'technologies' => ['technologies', '/technologies', TechnologyData::class, TechnologyData::class],
    'technologyDetections' => ['technologyDetections', '/technology-detections', TechnologyDetectionData::class, TechnologyDetectionData::class],
]);

it('lists an easm resource', function (string $method, string $endpoint, string $dtoClass) {
    $mockClient = new MockClient([MockResponse::make(easmPage([['id' => 1], ['id' => 2]]))]);
    LaravelPatrowl::withMockClient($mockClient);

    $items = iterator_to_array(LaravelPatrowl::{$method}()->all(['search' => 'acme'])->items(), false);

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBeInstanceOf($dtoClass)
        ->and($items[1]->id)->toBe(2);

    $mockClient->assertSent(fn (ListEasmRequest $request) => $request->resolveEndpoint() === $endpoint
        && $request->query()->get('search') === 'acme'
        && $request->query()->get('limit') === 100);
})->with('easm resources');

it('gets an easm resource by id', function (string $method, string $endpoint, string $listClass, string $dtoClass) {
    $mockClient = new MockClient([MockResponse::make(['id' => 42])]);
    LaravelPatrowl::withMockClient($mockClient);

    $item = LaravelPatrowl::{$method}()->get(42);

    expect($item)->toBeInstanceOf($dtoClass)
        ->and($item->id)->toBe(42);

    $mockClient->assertSent(fn (GetEasmRequest $request) => $request->resolveEndpoint() === "{$endpoint}/42");
})->with('easm resources');

it('sends the default organization id on easm lists', function () {
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([MockResponse::make(easmPage([]))]);
    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::domains()->all()->items());

    $mockClient->assertSent(fn (ListEasmRequest $request) => $request->query()->get('org_id') === 456);
});

it('follows page pagination on easm lists', function () {
    $mockClient = new MockClient([
        MockResponse::make(easmPage([['id' => 1]], 'https://dashboard.cloud.patrowl.io/api/auth/asns?page=2')),
        MockResponse::make(easmPage([['id' => 2]])),
    ]);
    LaravelPatrowl::withMockClient($mockClient);

    $ids = array_map(fn (AsnData $asn) => $asn->id, iterator_to_array(LaravelPatrowl::asns()->all()->items(), false));

    expect($ids)->toBe([1, 2]);
});

it('maps a domain with its dns records', function () {
    $domain = DomainData::fromApi([
        'id' => 42,
        'value' => 'example.com',
        'top_domain' => null,
        'www_domain' => 43,
        'registrar' => 'Example Registrar Inc.',
        'dnsrecords' => [['record_type' => 'A', 'value' => '203.0.113.10']],
        'expiration_dates' => ['2026-08-14T04:00:00Z'],
    ]);

    expect($domain->value)->toBe('example.com');
    expect($domain)
        ->topDomain->toBeNull()
        ->wwwDomain->toBe(43)
        ->registrar->toBe('Example Registrar Inc.')
        ->expirationDates->toBe(['2026-08-14T04:00:00Z'])
        ->creationDates->toBe([])
        ->and($domain->dnsRecords[0])->toBeInstanceOf(DnsRecordData::class)
        ->recordType->toBe('A')
        ->and($domain->toArray()['dnsrecords'])->toBe([['record_type' => 'A', 'value' => '203.0.113.10']]);
});

it('maps the provider of an ipv4', function () {
    $ipv4 = Ipv4Data::fromApi(['id' => 501, 'provider' => ['cdn' => null, 'cloud' => 'AWS', 'saas' => null, 'waf' => null]]);

    expect($ipv4->provider)->toBeInstanceOf(Ipv4ProviderData::class)
        ->cloud->toBe('AWS')
        ->cdn->toBeNull();
});

it('maps both sides of a resolution', function () {
    $resolution = ResolutionData::fromApi([
        'id' => 9001,
        'domain' => ['id' => 12, 'value' => 'www.acme.example'],
        'ipv4' => ['id' => 501, 'value' => '203.0.113.10', 'type' => 'static'],
        'resolves' => true,
    ]);

    expect($resolution->domain)->toBeInstanceOf(DomainInResolutionData::class)
        ->id->toBe(12)
        ->and($resolution->ipv4)->toBeInstanceOf(Ipv4InResolutionData::class)
        ->type->toBe('static')
        ->and($resolution->resolves)->toBeTrue();
});

it('maps the certificate and webservers of a service', function () {
    $service = ServiceData::fromApi([
        'id' => 616271,
        'port_number' => 443,
        'certificate' => ['id' => 88, 'serial_number' => '0A1B', 'is_expired' => false, 'is_mismatched' => false, 'is_selfsigned' => true],
        'webservers' => [
            ['id' => 452588, 'url' => 'http://13.249.228.75:443', 'server' => '', 'title' => '', 'path' => '/', 'jarm' => ''],
            ['id' => 455417, 'url' => 'https://13.249.228.75', 'server' => 'nginx', 'title' => 'Home', 'path' => '/', 'jarm' => ''],
        ],
    ]);

    expect($service->certificate)->toBeInstanceOf(CertificateInServiceData::class)
        ->isSelfsigned->toBeTrue()
        ->and($service->webservers)->toHaveCount(2)
        ->and($service->webservers[1])->toBeInstanceOf(WebServerInServiceData::class)
        ->server->toBe('nginx')
        ->and($service->toArray()['webservers'][0])->toBe(['id' => 452588, 'url' => 'http://13.249.228.75:443', 'server' => '', 'title' => '', 'path' => '/', 'jarm' => ''])
        ->and(ServiceData::fromApi(['id' => 1])->webservers)->toBe([]);
});

it('gets a service in detail with its banner', function () {
    $mockClient = new MockClient([MockResponse::make(['id' => 501, 'banner' => 'HTTP/1.1 200 OK'])]);
    LaravelPatrowl::withMockClient($mockClient);

    expect(LaravelPatrowl::services()->get(501))->toBeInstanceOf(ServiceDetailData::class)
        ->banner->toBe('HTTP/1.1 200 OK');
});

it('maps the service of a web server', function () {
    $webServer = WebServerData::fromApi([
        'id' => 42,
        'service' => ['id' => 100, 'port_number' => 443, 'protocol' => 'tcp', 'is_ssl' => true, 'state' => 'open'],
        'response_time' => 0.183,
    ]);

    expect($webServer->service)->toBeInstanceOf(WebServerServiceData::class)
        ->portNumber->toBe(443)
        ->state->toBe('open')
        ->and($webServer->responseTime)->toBe(0.183);
});

it('maps the services of a certificate', function () {
    $certificate = CertificateData::fromApi([
        'id' => 42,
        'services' => [['id' => 501, 'value' => 'app.example.com', 'asset_type' => 'domain', 'protocol' => 'tcp', 'port_number' => 443]],
        'data_parsed' => ['subject' => ['CN' => 'app.example.com']],
    ]);

    expect($certificate->services[0])->toBeInstanceOf(ServiceInCertificateData::class)
        ->assetType->toBe('domain')
        ->and($certificate->dataParsed)->toBe(['subject' => ['CN' => 'app.example.com']]);
});

it('maps the vendor of a product', function () {
    $product = ProductData::fromApi(['id' => 101, 'vendor' => ['id' => 7, 'name' => 'Microsoft'], 'type' => 'operating_system']);

    expect($product->vendor)->toBeInstanceOf(VendorInProductData::class)
        ->name->toBe('Microsoft');
});

it('maps the technology and service of a technology detection', function () {
    $detection = TechnologyDetectionData::fromApi([
        'id' => 5001,
        'value' => '203.0.113.10',
        'technology' => ['id' => 900, 'product' => ['id' => 12, 'name' => 'nginx', 'vendor' => ['id' => 4, 'name' => 'F5, Inc.']], 'version' => '1.25.3'],
        'service' => ['id' => 501, 'name' => 'https', 'protocol' => 'tcp', 'port_number' => 443],
        'source' => 'auto',
        'confidence' => 90,
        'is_relevant' => true,
    ]);

    expect($detection->technology)->toBeInstanceOf(TechnologyData::class)
        ->version->toBe('1.25.3')
        ->and($detection->technology->product)->toBeInstanceOf(ProductInTechnologyData::class)
        ->name->toBe('nginx')
        ->and($detection->technology->product->vendor?->name)->toBe('F5, Inc.')
        ->and($detection->service)->toBeInstanceOf(ServiceInTechnologyDetectionData::class)
        ->portNumber->toBe(443)
        ->and($detection->toArray()['technology']['product']['vendor']['name'])->toBe('F5, Inc.');
});

it('follows cursor pagination on cves', function () {
    $mockClient = new MockClient([
        MockResponse::make(['next' => 'https://dashboard.cloud.patrowl.io/api/auth/cves?cursor=cD0yMDI2&severity=critical', 'previous' => null, 'results' => [[
            'id' => 1,
            'identifier' => 'CVE-2026-0001',
            'severity' => 4,
            'cvss' => ['score' => 9.8, 'version' => 3],
            'threat_info' => ['is_exploitable' => true, 'is_in_the_news' => false, 'is_in_the_wild' => true, 'is_kev' => true],
            'technologies' => [['id' => 900, 'product' => 'nginx', 'version' => '1.25.3', 'vendor' => 'F5, Inc.', 'partial_cpe' => 'cpe:2.3:a:f5:nginx:1.25.3']],
        ]]]),
        MockResponse::make(['next' => null, 'previous' => null, 'results' => [['id' => 2, 'identifier' => 'CVE-2026-0002']]]),
    ]);
    LaravelPatrowl::withMockClient($mockClient);

    $cves = iterator_to_array(LaravelPatrowl::cves()->all(['severity' => 'critical'])->items(), false);

    expect($cves)->toHaveCount(2)
        ->and($cves[0])->toBeInstanceOf(CveData::class)
        ->severity->toBe(4)
        ->and($cves[0]->cvss)->toBeInstanceOf(CvssData::class)
        ->score->toBe(9.8)
        ->version->toBe(3)
        ->and($cves[0]->threatInfo)->toBeInstanceOf(ThreatInfoData::class)
        ->isKev->toBeTrue()
        ->isInTheNews->toBeFalse()
        ->and($cves[0]->technologies[0])->toBeInstanceOf(TechnologyInCveData::class)
        ->product->toBe('nginx')
        ->and($cves[0]->toArray()['threat_info']['is_in_the_wild'])->toBeTrue()
        ->and($cves[1]->identifier)->toBe('CVE-2026-0002');

    $mockClient->assertSentCount(2);
    expect($mockClient->getLastPendingRequest())->toBeInstanceOf(PendingRequest::class)
        ->and($mockClient->getLastPendingRequest()->query()->get('cursor'))->toBe('cD0yMDI2');
});

it('gets a cve by identifier with detail fields', function () {
    $mockClient = new MockClient([MockResponse::make([
        'id' => 55123,
        'identifier' => 'CVE-2026-12345',
        'severity' => 4,
        'cvss3_score' => 9.8,
        'epss_score' => 0.97,
        'cwe' => 'CWE-121',
        'exploits' => [['id' => 1, 'reference' => 'https://exploit.example', 'maturity' => 'poc', 'relevancy_level' => 3]],
    ])]);
    LaravelPatrowl::withMockClient($mockClient);

    $cve = LaravelPatrowl::cves()->get('CVE-2026-12345');

    expect($cve)->toBeInstanceOf(CveDetailData::class)
        ->identifier->toBe('CVE-2026-12345')
        ->severity->toBe(4)
        ->cvss3Score->toBe(9.8)
        ->cwe->toBe('CWE-121')
        ->and($cve->exploits[0])->toBeInstanceOf(ExploitData::class)
        ->maturity->toBe('poc')
        ->relevancyLevel->toBe(3);

    $mockClient->assertSent(fn (GetEasmRequest $request) => $request->resolveEndpoint() === '/cves/CVE-2026-12345');
});
