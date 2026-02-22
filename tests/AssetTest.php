<?php

use Illuminate\Support\Facades\Http;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupLiteData;
use Xternalsoft\LaravelPatrowl\Data\AssetInListData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Data\DomainLiteData;
use Xternalsoft\LaravelPatrowl\Data\AssetOwnerData;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Illuminate\Http\Client\Response;

it('can sync tags for an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/1/tags' => Http::response([], 200),
    ]);

    $response = LaravelPatrowl::syncAssetTags(1, [1, 2]);

    Http::assertSent(function ($request) {
        return $request->url() == 'https://dashboard.cloud.patrowl.io/api/auth/assets/1/tags' &&
               $request->method() == 'POST' &&
               $request->data() == ['tags' => [1, 2]];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});

it('can remove a specific asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/1/tags/1/' => Http::response([], 204),
    ]);

    $response = LaravelPatrowl::removeAssetTag(1, 1);

    Http::assertSent(function ($request) {
        return $request->url() == 'https://dashboard.cloud.patrowl.io/api/auth/assets/1/tags/1/' &&
               $request->method() == 'DELETE';
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(204);
});

it('can create an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets' => Http::response([
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

    $data = new CreateAssetData(
        value: 'new-asset.com',
        organization: 1,
        description: 'New Asset',
    );

    $asset = LaravelPatrowl::createAsset($data);

    Http::assertSent(function ($request) {
        return $request['org_id'] === 1;
    });

    expect($asset)
        ->toBeInstanceOf(AssetData::class)
        ->id->toBe(1)
        ->value->toBe('new-asset.com');
});

it('can create an asset with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    Http::fake([
        '*/assets' => Http::response([
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

    $data = new CreateAssetData(
        value: 'new-asset.com',
        description: 'New Asset',
    );

    LaravelPatrowl::createAsset($data);

    Http::assertSent(function ($request) {
        return $request['org_id'] === 456;
    });
});

it('can get an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/1' => Http::response([
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
                ]
            ],
            'groups' => [
                [
                    'id' => 1,
                    'title' => 'Group 1',
                    'description' => 'Description 1',
                ]
            ],
            'www_related_domain' => [ // This is now a single object, not an array
                'id' => 1,
                'value' => 'sub.asset.com',
                'protection' => ['status' => 'unprotected', 'availability' => 'available'],
                'outside_business_hours' => 0,
            ]
        ]),
    ]);

    $asset = LaravelPatrowl::getAsset(1);

    expect($asset)
        ->toBeInstanceOf(AssetData::class)
        ->groups->toBeArray()
        ->and($asset->groups[0])->toBeInstanceOf(AssetGroupLiteData::class)
        ->and($asset->groups[0]->title)->toBe('Group 1')
        ->and($asset->asset_owners)->toBeArray()
        ->and($asset->asset_owners[0])->toBeInstanceOf(AssetOwnerData::class)
        ->and($asset->asset_owners[0]->email)->toBe('test@example.com')
        ->and($asset->www_related_domain)->toBeInstanceOf(DomainLiteData::class) // Not an array
        ->and($asset->www_related_domain->value)->toBe('sub.asset.com') // No array index
        ->and($asset->www_related_domain->outside_business_hours->value)->toBe(0); // No array index
});

it('can get assets', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets*' => Http::response([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 1,
                    'value' => 'asset.com',
                ]
            ],
        ], 200),
    ]);

    $assets = iterator_to_array(LaravelPatrowl::getAssets());

    expect($assets)->toHaveCount(1)
        ->and($assets[0])->toBeInstanceOf(AssetInListData::class)
        ->and($assets[0]->id)->toBe(1);
});

it('can get assets with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    Http::fake([
        '*/assets*' => Http::response([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    iterator_to_array(LaravelPatrowl::getAssets());

    Http::assertSent(function ($request) {
        return $request->url() === 'https://dashboard.cloud.patrowl.io/api/auth/assets?org_id=456&limit=100&page=1';
    });
});

it('can get assets with default limit', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.limit', 50);

    Http::fake([
        '*/assets*' => Http::response([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    iterator_to_array(LaravelPatrowl::getAssets());

    Http::assertSent(function ($request) {
        return $request->url() === 'https://dashboard.cloud.patrowl.io/api/auth/assets?limit=50&page=1';
    });
});

it('can add a tag to an asset', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/1/tags/add' => Http::response([], 200),
    ]);

    $data = new AddTagToAssetData(
        value: 'my-tag',
        organization: 1
    );

    $response = LaravelPatrowl::addTagToAsset(1, $data);

    Http::assertSent(function ($request) {
        return $request->url() == 'https://dashboard.cloud.patrowl.io/api/auth/assets/1/tags/add' &&
               $request->method() == 'POST' &&
               $request->data() == ['value' => 'my-tag', 'organization' => 1];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});

it('can add a tag to an asset with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    Http::fake([
        '*/assets/1/tags/add' => Http::response([], 200),
    ]);

    $data = new AddTagToAssetData(
        value: 'my-tag'
    );

    $response = LaravelPatrowl::addTagToAsset(1, $data);

    Http::assertSent(function ($request) {
        return $request->url() == 'https://dashboard.cloud.patrowl.io/api/auth/assets/1/tags/add' &&
               $request->method() == 'POST' &&
               $request->data() == ['value' => 'my-tag', 'organization' => 456];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(200);
});
