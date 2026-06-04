<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetTagData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\CreateAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagsRequest;

it('can create an asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        CreateAssetTagRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 1,
        ], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetTagData(
        value: 'my-tag',
        organization: 1,
        id: 0
    );

    $assetTag = LaravelPatrowl::assetTags()->create($data);

    $mockClient->assertSent(function (CreateAssetTagRequest $request) {
        return $request->body()->all()['id'] === 0;
    });

    expect($assetTag)
        ->toBeInstanceOf(AssetTagData::class)
        ->id->toBe(1)
        ->value->toBe('my-tag')
        ->organization->toBe(1);
});

it('can create an asset tag with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        CreateAssetTagRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 456,
        ], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetTagData(
        value: 'my-tag',
        id: 0
    );

    LaravelPatrowl::assetTags()->create($data);

    $mockClient->assertSent(function (CreateAssetTagRequest $request) {
        return $request->body()->all()['organization'] === 456 && $request->body()->all()['id'] === 0;
    });
});

it('can get an asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetAssetTagRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 1,
        ]),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $assetTag = LaravelPatrowl::assetTags()->get(1);

    expect($assetTag)
        ->toBeInstanceOf(AssetTagData::class)
        ->id->toBe(1)
        ->value->toBe('my-tag')
        ->organization->toBe(1);
});

it('can get asset tags', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetAssetTagsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'value' => 'my-tag',
                    'organization' => 1,
                ],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $assetTags = iterator_to_array(LaravelPatrowl::assetTags()->all()->items());

    expect($assetTags)->toHaveCount(1);
    expect($assetTags[0])
        ->toBeInstanceOf(AssetTagData::class)
        ->id->toBe(1)
        ->value->toBe('my-tag')
        ->organization->toBe(1);
});

it('can get asset tags with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        GetAssetTagsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::assetTags()->all()->items());

    $mockClient->assertSent(function (GetAssetTagsRequest $request) {
        return $request->query()->all() === ['org_id' => 456, 'limit' => 100, 'page' => 1];
    });
});
