<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\ProductData;
use Xternalsoft\LaravelPatrowl\Data\TechnologyAggregateData;
use Xternalsoft\LaravelPatrowl\Data\VendorData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetProductsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetTechnologyAggregatesRequest;
use Xternalsoft\LaravelPatrowl\Requests\Technologies\GetVendorsRequest;

it('maps ProductData from API response', function () {
    $data = [
        'id' => 1,
        'name' => 'Apache HTTP Server',
        'vendor' => 'Apache Software Foundation',
    ];

    $product = ProductData::fromApi($data);

    expect($product->id)->toBe(1)
        ->and($product->name)->toBe('Apache HTTP Server')
        ->and($product->vendor)->toBe('Apache Software Foundation')
        ->and($product->toArray())->toBe($data);
});

it('maps VendorData from API response', function () {
    $data = [
        'id' => 1,
        'name' => 'Apache',
    ];

    $vendor = VendorData::fromApi($data);

    expect($vendor->id)->toBe(1)
        ->and($vendor->name)->toBe('Apache')
        ->and($vendor->toArray())->toBe($data);
});

it('maps TechnologyAggregateData from API response', function () {
    $data = [
        'id' => 1,
        'assets' => 12,
        'cves' => 3,
        'last_seen_at' => '2026-08-10T12:00:00Z',
    ];

    $aggregate = TechnologyAggregateData::fromApi($data);

    expect($aggregate->id)->toBe(1)
        ->and($aggregate->assets)->toBe(12)
        ->and($aggregate->cves)->toBe(3)
        ->and($aggregate->lastSeenAt)->toBe('2026-08-10T12:00:00Z')
        ->and($aggregate->toArray())->toBe($data);
});

it('resolves correct endpoints and query params', function () {
    $productsReq = new GetProductsRequest(['search' => 'PHP'], orgId: 1);
    expect($productsReq->resolveEndpoint())->toBe('/asm/product/');

    $vendorsReq = new GetVendorsRequest(['ordering' => 'name'], orgId: 1);
    expect($vendorsReq->resolveEndpoint())->toBe('/asm/vendor/');

    $aggregatesReq = new GetTechnologyAggregatesRequest([1, 2], ['asset_id' => 10], orgId: 1);
    expect($aggregatesReq->resolveEndpoint())->toBe('/asm/technology/aggregates/')
        ->and($aggregatesReq->getMethod()->value)->toBe('POST');
});

it('GetProductsRequest builds the correct request endpoint and query parameters', function () {
    $request = new GetProductsRequest(
        queryParams: ['search' => 'Apache', 'ordering' => 'name'],
        orgId: 5,
        limit: 20
    );

    expect($request->resolveEndpoint())->toBe('/asm/product/')
        ->and($request->getMethod()->value)->toBe('GET')
        ->and($request->query()->all())->toBe([
            'search' => 'Apache',
            'ordering' => 'name',
            'org_id' => 5,
            'limit' => 20,
        ]);
});

it('GetVendorsRequest builds the correct request endpoint and query parameters', function () {
    $request = new GetVendorsRequest(
        queryParams: ['search' => 'Apache', 'ordering' => 'name'],
        orgId: 5,
        limit: 20
    );

    expect($request->resolveEndpoint())->toBe('/asm/vendor/')
        ->and($request->getMethod()->value)->toBe('GET')
        ->and($request->query()->all())->toBe([
            'search' => 'Apache',
            'ordering' => 'name',
            'org_id' => 5,
            'limit' => 20,
        ]);
});

it('GetTechnologyAggregatesRequest builds the correct request endpoint, body and query parameters', function () {
    $request = new GetTechnologyAggregatesRequest(
        ids: [1, 2],
        queryParams: ['asset_id' => 10],
        orgId: 5
    );

    expect($request->resolveEndpoint())->toBe('/asm/technology/aggregates/')
        ->and($request->getMethod()->value)->toBe('POST')
        ->and($request->body()->all())->toBe([
            'ids' => [1, 2],
        ])
        ->and($request->query()->all())->toBe([
            'asset_id' => 10,
            'org_id' => 5,
        ]);
});

it('correctly maps response items for Products, Vendors, and TechnologyAggregates', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetProductsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'name' => 'Apache HTTP Server',
                    'vendor' => 'Apache Software Foundation',
                ],
            ],
        ], 200),
        GetVendorsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'name' => 'Apache',
                ],
            ],
        ], 200),
        GetTechnologyAggregatesRequest::class => MockResponse::make([
            'counts' => [
                [
                    'id' => 1,
                    'assets' => 12,
                    'cves' => 3,
                    'last_seen_at' => '2026-08-10T12:00:00Z',
                ],
            ],
        ], 200),
    ]);

    $connector = new Xternalsoft\LaravelPatrowl\LaravelPatrowl('fake-token', 'https://example.com');
    $connector->withMockClient($mockClient);

    $response = $connector->send(new GetProductsRequest());
    $products = $response->dto();
    expect($products)->toBeArray()
        ->and($products)->toHaveCount(1)
        ->and($products[0])->toBeInstanceOf(ProductData::class)
        ->and($products[0]->id)->toBe(1);

    $response = $connector->send(new GetVendorsRequest());
    $vendors = $response->dto();
    expect($vendors)->toBeArray()
        ->and($vendors)->toHaveCount(1)
        ->and($vendors[0])->toBeInstanceOf(VendorData::class)
        ->and($vendors[0]->id)->toBe(1);

    $response = $connector->send(new GetTechnologyAggregatesRequest([1]));
    $aggregates = $response->dto();
    expect($aggregates)->toBeArray()
        ->and($aggregates)->toHaveCount(1)
        ->and($aggregates[0])->toBeInstanceOf(TechnologyAggregateData::class)
        ->and($aggregates[0]->id)->toBe(1);
});

it('can list products, list vendors, and fetch aggregates', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetProductsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                ['id' => 1, 'name' => 'Apache', 'vendor' => 'Apache SF'],
            ],
        ], 200),
        GetVendorsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                ['id' => 1, 'name' => 'Apache SF'],
            ],
        ], 200),
        GetTechnologyAggregatesRequest::class => MockResponse::make([
            'counts' => [
                ['id' => 1, 'assets' => 5, 'cves' => 2, 'last_seen_at' => null],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    // Test products
    $products = iterator_to_array(LaravelPatrowl::technologies()->products()->items());
    expect($products)->toHaveCount(1)
        ->and($products[0]->name)->toBe('Apache');

    // Test vendors
    $vendors = iterator_to_array(LaravelPatrowl::technologies()->vendors()->items());
    expect($vendors)->toHaveCount(1)
        ->and($vendors[0]->name)->toBe('Apache SF');

    // Test aggregates
    $aggregates = LaravelPatrowl::technologies()->aggregates([1]);
    expect($aggregates)->toHaveCount(1)
        ->and($aggregates[0]->assets)->toBe(5)
        ->and($aggregates[0]->cves)->toBe(2);
});
