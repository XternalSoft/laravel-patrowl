<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\ControlAssetData;
use Xternalsoft\LaravelPatrowl\Data\ControlData;
use Xternalsoft\LaravelPatrowl\Data\ControlLiteData;
use Xternalsoft\LaravelPatrowl\Data\ControlVulnerabilityData;
use Xternalsoft\LaravelPatrowl\Enums\ControlResultEnum;
use Xternalsoft\LaravelPatrowl\Enums\ControlStatusEnum;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Controls\GetControlRequest;
use Xternalsoft\LaravelPatrowl\Requests\Controls\GetControlsRequest;

it('can get controls list', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetControlsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 123,
                    'title' => 'Control Title',
                    'status' => 1,
                    'result' => 1,
                    'cve_identifiers' => ['CVE-2026-0001'],
                    'cvss_vector' => 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H',
                    'cvss_score' => 9.8,
                    'severity' => 'critical',
                    'description' => 'Test Control Description',
                    'references' => ['https://example.com'],
                    'vendor' => 'Test Vendor',
                    'product' => 'Test Product',
                    'products' => ['Test Product'],
                    'started_at' => '2026-06-04T12:00:00Z',
                    'finished_at' => '2026-06-04T13:00:00Z',
                    'created_at' => '2026-06-04T11:00:00Z',
                    'updated_at' => '2026-06-04T14:00:00Z',
                ],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $controls = iterator_to_array(LaravelPatrowl::controls()->all()->items());

    expect($controls)->toHaveCount(1);

    $control = $controls[0];
    expect($control)
        ->toBeInstanceOf(ControlLiteData::class)
        ->id->toBe(123)
        ->title->toBe('Control Title')
        ->status->toBe(ControlStatusEnum::Finished)
        ->result->toBe(ControlResultEnum::NotImpacted)
        ->cveIdentifiers->toBe(['CVE-2026-0001'])
        ->cvssVector->toBe('CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H')
        ->cvssScore->toBe(9.8)
        ->severity->toBe('critical')
        ->description->toBe('Test Control Description')
        ->references->toBe(['https://example.com'])
        ->vendor->toBe('Test Vendor')
        ->product->toBe('Test Product')
        ->products->toBe(['Test Product'])
        ->startedAt->toBe('2026-06-04T12:00:00Z')
        ->finishedAt->toBe('2026-06-04T13:00:00Z')
        ->createdAt->toBe('2026-06-04T11:00:00Z')
        ->updatedAt->toBe('2026-06-04T14:00:00Z');
});

it('can filter controls list with query parameters', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetControlsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::controls()->all([
        'severity' => 'critical',
        'search' => 'CVE',
    ])->items());

    $mockClient->assertSent(function (GetControlsRequest $request) {
        return $request->query()->all() === [
            'severity' => 'critical',
            'search' => 'CVE',
            'limit' => 100,
            'page' => 1,
        ];
    });
});

it('can get a specific control in detail', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetControlRequest::class => MockResponse::make([
            'id' => 123,
            'title' => 'Control Title',
            'status' => 1,
            'result' => 1,
            'cve_identifiers' => ['CVE-2026-0001'],
            'cvss_vector' => 'CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H',
            'cvss_score' => 9.8,
            'severity' => 'critical',
            'description' => 'Test Control Description',
            'references' => ['https://example.com'],
            'vendor' => 'Test Vendor',
            'product' => 'Test Product',
            'products' => ['Test Product'],
            'assets_impacted' => [
                ['id' => 10, 'value' => 'impacted-asset.com'],
            ],
            'assets_warning' => [
                ['id' => 20, 'value' => 'warning-asset.com'],
            ],
            'vulnerabilities' => [
                [
                    'id' => 30,
                    'asset' => 10,
                    'asset_value' => 'impacted-asset.com',
                    'title' => 'Vulnerability Title',
                    'severity' => 'high',
                ],
            ],
            'started_at' => '2026-06-04T12:00:00Z',
            'finished_at' => '2026-06-04T13:00:00Z',
            'created_at' => '2026-06-04T11:00:00Z',
            'updated_at' => '2026-06-04T14:00:00Z',
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $control = LaravelPatrowl::controls()->get(123);

    expect($control)
        ->toBeInstanceOf(ControlData::class)
        ->id->toBe(123)
        ->title->toBe('Control Title')
        ->status->toBe(ControlStatusEnum::Finished)
        ->result->toBe(ControlResultEnum::NotImpacted)
        ->cveIdentifiers->toBe(['CVE-2026-0001'])
        ->cvssVector->toBe('CVSS:3.1/AV:N/AC:L/PR:N/UI:N/S:U/C:H/I:H/A:H')
        ->cvssScore->toBe(9.8)
        ->severity->toBe('critical')
        ->description->toBe('Test Control Description')
        ->references->toBe(['https://example.com'])
        ->vendor->toBe('Test Vendor')
        ->product->toBe('Test Product')
        ->products->toBe(['Test Product'])
        ->startedAt->toBe('2026-06-04T12:00:00Z')
        ->finishedAt->toBe('2026-06-04T13:00:00Z')
        ->createdAt->toBe('2026-06-04T11:00:00Z')
        ->updatedAt->toBe('2026-06-04T14:00:00Z');

    expect($control->assetsImpacted)->toHaveCount(1);
    expect($control->assetsImpacted[0])
        ->toBeInstanceOf(ControlAssetData::class)
        ->id->toBe(10)
        ->value->toBe('impacted-asset.com');

    expect($control->assetsWarning)->toHaveCount(1);
    expect($control->assetsWarning[0])
        ->toBeInstanceOf(ControlAssetData::class)
        ->id->toBe(20)
        ->value->toBe('warning-asset.com');

    expect($control->vulnerabilities)->toHaveCount(1);
    expect($control->vulnerabilities[0])
        ->toBeInstanceOf(ControlVulnerabilityData::class)
        ->id->toBe(30)
        ->asset->toBe(10)
        ->assetValue->toBe('impacted-asset.com')
        ->title->toBe('Vulnerability Title')
        ->severity->toBe('high');
});

it('can handle partial and missing response fields gracefully in ControlLiteData', function () {
    // Only the bare minimum fields or missing fields are provided
    $data = [
        'id' => 123,
        'title' => 'Minimum Control',
    ];

    $controlLite = ControlLiteData::fromApi($data);

    expect($controlLite)
        ->toBeInstanceOf(ControlLiteData::class)
        ->id->toBe(123)
        ->title->toBe('Minimum Control')
        ->status->toBeNull()
        ->result->toBeNull()
        ->cveIdentifiers->toBe([])
        ->cvssVector->toBeNull()
        ->cvssScore->toBeNull()
        ->severity->toBeNull()
        ->description->toBeNull()
        ->references->toBe([])
        ->vendor->toBeNull()
        ->product->toBeNull()
        ->products->toBe([])
        ->startedAt->toBeNull()
        ->finishedAt->toBeNull()
        ->createdAt->toBeNull()
        ->updatedAt->toBeNull();
});

it('can handle partial and missing response fields gracefully in ControlData', function () {
    // Only the bare minimum fields or missing relationship arrays are provided
    $data = [
        'id' => 123,
        'title' => 'Minimum Control Detail',
    ];

    $control = ControlData::fromApi($data);

    expect($control)
        ->toBeInstanceOf(ControlData::class)
        ->id->toBe(123)
        ->title->toBe('Minimum Control Detail')
        ->status->toBeNull()
        ->result->toBeNull()
        ->cveIdentifiers->toBe([])
        ->cvssVector->toBeNull()
        ->cvssScore->toBeNull()
        ->severity->toBeNull()
        ->description->toBeNull()
        ->references->toBe([])
        ->vendor->toBeNull()
        ->product->toBeNull()
        ->products->toBe([])
        ->assetsImpacted->toBe([])
        ->assetsWarning->toBe([])
        ->vulnerabilities->toBe([])
        ->startedAt->toBeNull()
        ->finishedAt->toBeNull()
        ->createdAt->toBeNull()
        ->updatedAt->toBeNull();
});
