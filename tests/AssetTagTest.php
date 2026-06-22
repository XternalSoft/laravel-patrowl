<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkAssociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkDissociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetTagsRequest;

it('can bulk associate asset tags with assets', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        BulkAssociateTagsRequest::class => MockResponse::make([
            'status' => 'success',
            'message' => 'Associated 1 tags with 1 assets.',
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new BulkAssociateTagsData(
        assetIds: [123],
        tagIds: [456],
        organizationId: 1
    );

    $response = LaravelPatrowl::assetTags()->associate($data);

    $mockClient->assertSent(function (BulkAssociateTagsRequest $request) {
        return $request->body()->all() === [
            'asset_ids' => [123],
            'tag_ids' => [456],
            'organization_id' => 1,
        ];
    });

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->json('status'))->toBe('success');
});

it('can bulk associate asset tags with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        BulkAssociateTagsRequest::class => MockResponse::make([
            'status' => 'success',
            'message' => 'Associated 1 tags with 1 assets.',
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new BulkAssociateTagsData(
        assetIds: [123],
        tagIds: [456]
    );

    LaravelPatrowl::assetTags()->associate($data);

    $mockClient->assertSent(function (BulkAssociateTagsRequest $request) {
        return $request->body()->all() === [
            'asset_ids' => [123],
            'tag_ids' => [456],
            'organization_id' => 456,
        ];
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

it('can get all tags from /tags/ endpoint', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetTagsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'value' => 'my-tag',
                    'description' => 'Some description',
                    'organization' => 1,
                    'assets' => [],
                    'asset_groups' => [],
                    'created_by' => null,
                    'created_at' => '2023-01-01T00:00:00Z',
                ],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $tags = iterator_to_array(LaravelPatrowl::assetTags()->tags()->items());

    expect($tags)->toHaveCount(1);
    expect($tags[0])
        ->toBeInstanceOf(Xternalsoft\LaravelPatrowl\Data\TagData::class)
        ->id->toBe(1)
        ->value->toBe('my-tag')
        ->description->toBe('Some description')
        ->organization->toBe(1);
});

it('can bulk dissociate asset tags from assets without organization context', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', null);

    $mockClient = new MockClient([
        BulkDissociateTagsRequest::class => MockResponse::make([
            'status' => 'success',
            'message' => 'Dissociated 1 tags from 1 assets.',
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $response = LaravelPatrowl::assetTags()->dissociate([123], [456]);

    $mockClient->assertSent(function (BulkDissociateTagsRequest $request) {
        return $request->body()->all() === [
            'asset_ids' => [123],
            'tag_ids' => [456],
        ];
    });

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->json('status'))->toBe('success');
});

it('can bulk dissociate asset tags from assets with organization context', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        BulkDissociateTagsRequest::class => MockResponse::make([
            'status' => 'success',
            'message' => 'Dissociated 1 tags from 1 assets.',
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $response = LaravelPatrowl::assetTags()->dissociate([123], [456]);

    $mockClient->assertSent(function (BulkDissociateTagsRequest $request) {
        return $request->body()->all() === [
            'asset_ids' => [123],
            'tag_ids' => [456],
            'organization_id' => 456,
        ];
    });

    expect($response)->toBeInstanceOf(Response::class)
        ->and($response->status())->toBe(200)
        ->and($response->json('status'))->toBe('success');
});
