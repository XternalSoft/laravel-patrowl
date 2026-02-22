<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetTagData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;

it('can create an asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');
    Http::fake([
        '*/assets/tags/' => Http::response([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 1,
        ], 201),
    ]);

    $data = new CreateAssetTagData(
        value: 'my-tag',
        organization: 1,
        id: 0
    );

    $assetTag = LaravelPatrowl::createAssetTag($data);

    Http::assertSent(function ($request) {
        return $request['id'] === 0;
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

    Http::fake([
        '*/assets/tags/' => Http::response([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 456,
        ], 201),
    ]);

    $data = new CreateAssetTagData(
        value: 'my-tag',
        id: 0
    );

    LaravelPatrowl::createAssetTag($data);

    Http::assertSent(function ($request) {
        return $request['organization'] === 456 && $request['id'] === 0;
    });
});

it('can get an asset tag', function () {
    config()->set('patrowl.api_token', 'fake-token');
    Http::fake([
        '*/assets/tags/1/' => Http::response([
            'id' => 1,
            'value' => 'my-tag',
            'organization' => 1,
        ]),
    ]);

    $assetTag = LaravelPatrowl::getAssetTag(1);

    expect($assetTag)
        ->toBeInstanceOf(AssetTagData::class)
        ->id->toBe(1)
        ->value->toBe('my-tag')
        ->organization->toBe(1);
});

it('can get asset tags', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/tags/*' => Http::response([
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

    $assetTags = iterator_to_array(LaravelPatrowl::getAssetTags());

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

    Http::fake([
        '*/assets/tags/*' => Http::response([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    iterator_to_array(LaravelPatrowl::getAssetTags());

    Http::assertSent(function ($request) {
        return $request->url() === 'https://dashboard.cloud.patrowl.io/api/auth/assets/tags/?org_id=456&limit=100&page=1';
    });
});
