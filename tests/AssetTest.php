<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupLiteData;
use Xternalsoft\LaravelPatrowl\Data\AssetInListData;
use Xternalsoft\LaravelPatrowl\Data\AssetOwnerData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Data\DomainLiteData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Assets\AddTagToAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\CreateAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\RemoveAssetTagFromAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\SyncAssetTagsRequest;

it('can sync tags for an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        SyncAssetTagsRequest::class => MockResponse::make([], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $response = LaravelPatrowl::assets()->syncTags(1, [1, 2]);

    $mockClient->assertSent(SyncAssetTagsRequest::class);
    $mockClient->assertSent(function (SyncAssetTagsRequest $request) {
        return $request->resolveEndpoint() === '/assets/1/tags' &&
               $request->body()->all() === ['tags' => [1, 2]];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});

it('can remove a specific asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        RemoveAssetTagFromAssetRequest::class => MockResponse::make([], 204),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $response = LaravelPatrowl::assets()->removeTag(1, 1);

    $mockClient->assertSent(RemoveAssetTagFromAssetRequest::class);
    $mockClient->assertSent(function (RemoveAssetTagFromAssetRequest $request) {
        return $request->resolveEndpoint() === '/assets/1/tags/1/';
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(204);
});

it('can create an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        CreateAssetRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'new-asset.com',
            'criticality' => 1,
            'type' => 'domain',
            'description' => 'New Asset',
            'exposure' => 'external',
            'is_active' => true,
            'score' => 0,
            'protection' => ['status' => 'unprotected', 'availability' => 'available'],
            'created_by' => 'test',
            'score_level' => 0,
            'ip_state' => 'running',
            'liveness' => 'up',
            'has_webservers' => false,
        ], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetData(
        value: 'new-asset.com',
        organization: 1,
        description: 'New Asset',
    );

    $asset = LaravelPatrowl::assets()->create($data);

    $mockClient->assertSent(function (CreateAssetRequest $request) {
        return $request->body()->all()['org_id'] === 1;
    });

    expect($asset)
        ->toBeInstanceOf(AssetData::class)
        ->id->toBe(1)
        ->value->toBe('new-asset.com');
});

it('can create an asset with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        CreateAssetRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'new-asset.com',
            'criticality' => 1,
            'type' => 'domain',
            'description' => 'New Asset',
            'exposure' => 'external',
            'is_active' => true,
            'score' => 0,
            'protection' => ['status' => 'unprotected', 'availability' => 'available'],
            'created_by' => 'test',
            'score_level' => 0,
            'ip_state' => 'running',
            'liveness' => 'up',
            'has_webservers' => false,
        ], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetData(
        value: 'new-asset.com',
        description: 'New Asset',
    );

    LaravelPatrowl::assets()->create($data);

    $mockClient->assertSent(function (CreateAssetRequest $request) {
        return $request->body()->all()['org_id'] === 456;
    });
});

it('can get an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetAssetRequest::class => MockResponse::make([
            'id' => 1,
            'value' => 'asset.com',
            'criticality' => 1,
            'type' => 'domain',
            'description' => 'Asset',
            'exposure' => 'external',
            'is_active' => true,
            'score' => 0,
            'protection' => ['status' => 'unprotected', 'availability' => 'available'],
            'created_by' => 'test',
            'score_level' => 0,
            'ip_state' => 'running',
            'liveness' => 'up',
            'has_webservers' => false,
            'asset_owners' => [
                [
                    'id' => 1,
                    'email' => 'test@example.com',
                ],
            ],
            'groups' => [
                [
                    'id' => 1,
                    'title' => 'Group 1',
                    'description' => 'Description 1',
                ],
            ],
            'www_related_domain' => [
                'id' => 1,
                'value' => 'sub.asset.com',
                'protection' => ['status' => 'unprotected', 'availability' => 'available'],
                'outside_business_hours' => 0,
            ],
        ]),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $asset = LaravelPatrowl::assets()->get(1);

    expect($asset)
        ->toBeInstanceOf(AssetData::class)
        ->groups->toBeArray()
        ->and($asset->groups[0])->toBeInstanceOf(AssetGroupLiteData::class)
        ->and($asset->groups[0]->title)->toBe('Group 1')
        ->and($asset->asset_owners)->toBeArray()
        ->and($asset->asset_owners[0])->toBeInstanceOf(AssetOwnerData::class)
        ->and($asset->asset_owners[0]->email)->toBe('test@example.com')
        ->and($asset->www_related_domain)->toBeInstanceOf(DomainLiteData::class)
        ->and($asset->www_related_domain->value)->toBe('sub.asset.com')
        ->and($asset->www_related_domain->outside_business_hours->value)->toBe(0);
});

it('can get assets', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetAssetsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'value' => 'asset.com',
                ],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $assets = iterator_to_array(LaravelPatrowl::assets()->all()->items());

    expect($assets)->toHaveCount(1)
        ->and($assets[0])->toBeInstanceOf(AssetInListData::class)
        ->and($assets[0]->id)->toBe(1);
});

it('can get assets with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        GetAssetsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::assets()->all()->items());

    $mockClient->assertSent(function (GetAssetsRequest $request) {
        return $request->query()->all() === ['org_id' => 456, 'limit' => 100, 'page' => 1];
    });
});

it('can get assets with default limit', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.limit', 50);

    $mockClient = new MockClient([
        GetAssetsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::assets()->all()->items());

    $mockClient->assertSent(function (GetAssetsRequest $request) {
        return $request->query()->all() === ['limit' => 50, 'page' => 1];
    });
});

it('can add a tag to an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        AddTagToAssetRequest::class => MockResponse::make([], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new AddTagToAssetData(
        value: 'my-tag',
        organization: 1
    );

    $response = LaravelPatrowl::assets()->addTag(1, $data);

    $mockClient->assertSent(function (AddTagToAssetRequest $request) {
        return $request->resolveEndpoint() === '/assets/1/tags/add' &&
               $request->body()->all() === ['value' => 'my-tag', 'organization' => 1];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});

it('can add a tag to an asset with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        AddTagToAssetRequest::class => MockResponse::make([], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new AddTagToAssetData(
        value: 'my-tag'
    );

    $response = LaravelPatrowl::assets()->addTag(1, $data);

    $mockClient->assertSent(function (AddTagToAssetRequest $request) {
        return $request->body()->all() === ['value' => 'my-tag', 'organization' => 456];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});
