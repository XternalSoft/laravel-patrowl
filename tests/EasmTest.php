<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
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
use Xternalsoft\LaravelPatrowl\Data\Easm\ThreatInfoData;
use Xternalsoft\LaravelPatrowl\Data\Easm\VendorData;
use Xternalsoft\LaravelPatrowl\Data\Easm\WebServerData;
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
    'domains' => ['domains', '/domains', DomainData::class],
    'ipv4s' => ['ipv4s', '/ipv4s', Ipv4Data::class],
    'resolutions' => ['resolutions', '/resolutions', ResolutionData::class],
    'asns' => ['asns', '/asns', AsnData::class],
    'services' => ['services', '/services', ServiceData::class],
    'webServers' => ['webServers', '/webservers', WebServerData::class],
    'certificates' => ['certificates', '/certificates', CertificateData::class],
    'vendors' => ['vendors', '/vendors', VendorData::class],
    'products' => ['products', '/products', ProductData::class],
    'technologies' => ['technologies', '/technologies', TechnologyData::class],
    'technologyDetections' => ['technologyDetections', '/technology-detections', TechnologyDetectionData::class],
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

it('gets an easm resource by id', function (string $method, string $endpoint, string $dtoClass) {
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

it('maps a domain with whois and dns data', function () {
    $domain = DomainData::fromApi([
        'id' => 42,
        'value' => 'example.com',
        'top_domain' => null,
        'www_domain' => 43,
        'registrar' => 'Example Registrar Inc.',
        'dnsrecords' => [['record_type' => 'A', 'value' => '203.0.113.10']],
        'expiration_dates' => ['2026-08-14T04:00:00Z'],
        'name_servers' => ['ns1.example.com'],
    ]);

    expect($domain->value)->toBe('example.com');
    expect($domain)
        ->topDomain->toBeNull()
        ->wwwDomain->toBe(43)
        ->registrar->toBe('Example Registrar Inc.')
        ->dnsRecords->toBe([['record_type' => 'A', 'value' => '203.0.113.10']])
        ->expirationDates->toBe(['2026-08-14T04:00:00Z'])
        ->creationDates->toBe([])
        ->and($domain->toArray())
        ->toHaveKey('dnsrecords')
        ->toHaveKey('expiration_dates');
});

it('maps the technology of a technology detection', function () {
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
        ->and($detection->technology->product['name'])->toBe('nginx')
        ->and($detection->toArray()['technology']['version'])->toBe('1.25.3');
});

it('follows cursor pagination on cves', function () {
    $mockClient = new MockClient([
        MockResponse::make(['next' => 'https://dashboard.cloud.patrowl.io/api/auth/cves?cursor=cD0yMDI2&severity=critical', 'previous' => null, 'results' => [['id' => 1, 'identifier' => 'CVE-2026-0001']]]),
        MockResponse::make(['next' => null, 'previous' => null, 'results' => [['id' => 2, 'identifier' => 'CVE-2026-0002']]]),
    ]);
    LaravelPatrowl::withMockClient($mockClient);

    $cves = iterator_to_array(LaravelPatrowl::cves()->all(['severity' => 'critical'])->items(), false);

    expect($cves)->toHaveCount(2)
        ->and($cves[0])->toBeInstanceOf(CveData::class)
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
        'cvss' => ['score' => 9.8, 'version' => 3],
        'cvss3_score' => 9.8,
        'threat_info' => ['is_exploitable' => true, 'is_in_the_news' => false, 'is_in_the_wild' => true, 'is_kev' => true],
        'epss_score' => 0.97,
        'cwe' => 'CWE-121',
        'exploits' => [['id' => 1, 'reference' => 'https://exploit.example']],
    ])]);
    LaravelPatrowl::withMockClient($mockClient);

    $cve = LaravelPatrowl::cves()->get('CVE-2026-12345');

    expect($cve)->toBeInstanceOf(CveData::class)
        ->identifier->toBe('CVE-2026-12345')
        ->severity->toBe(4)
        ->cvss->toBe(['score' => 9.8, 'version' => 3])
        ->cvss3Score->toBe(9.8)
        ->cwe->toBe('CWE-121')
        ->exploits->toHaveCount(1)
        ->and($cve->threatInfo)->toBeInstanceOf(ThreatInfoData::class)
        ->isExploitable->toBeTrue()
        ->isInTheNews->toBeFalse()
        ->isKev->toBeTrue()
        ->and($cve->toArray()['threat_info']['is_in_the_wild'])->toBeTrue();

    $mockClient->assertSent(fn (GetEasmRequest $request) => $request->resolveEndpoint() === '/cves/CVE-2026-12345');
});
