<?php

use Illuminate\Support\Facades\Http;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupInListData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;

it('can get asset groups', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/group/*' => Http::response([
            'count' => 2,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'title' => 'Group 1',
                    'id' => 1,
                    'description' => 'Description 1',
                    'scope' => 'private',
                    'tags' => [],
                    'owners' => [],
                    'assets_count' => 5,
                    'created_at' => '2023-01-01T00:00:00Z',
                    'updated_at' => '2023-01-01T00:00:00Z',
                    'todos_count' => 0,
                    'comments_count' => 0,
                ],
                [
                    'title' => 'Group 2',
                    'id' => 2,
                    'description' => 'Description 2',
                    'scope' => 'public',
                    'tags' => [],
                    'owners' => [],
                    'assets_count' => 10,
                    'created_at' => '2023-01-02T00:00:00Z',
                    'updated_at' => '2023-01-02T00:00:00Z',
                    'todos_count' => 0,
                    'comments_count' => 0,
                ],
            ],
        ], 200),
    ]);

    $assetGroups = iterator_to_array(LaravelPatrowl::getAssetGroups());

    expect($assetGroups)->toHaveCount(2);

    expect($assetGroups[0])
        ->toBeInstanceOf(AssetGroupInListData::class)
        ->id->toBe(1)
        ->title->toBe('Group 1');

    expect($assetGroups[1])
        ->toBeInstanceOf(AssetGroupInListData::class)
        ->id->toBe(2)
        ->title->toBe('Group 2');
});

it('can get asset groups with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    Http::fake([
        '*/assets/group/*' => Http::response([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    iterator_to_array(LaravelPatrowl::getAssetGroups());

    Http::assertSent(function ($request) {
        return $request->url() === 'https://dashboard.cloud.patrowl.io/api/auth/assets/group/?org_id=456&limit=100&page=1';
    });
});

it('can create an asset group', function () {
    config()->set('patrowl.api_token', 'fake-token');

    Http::fake([
        '*/assets/group/' => Http::response([
            'id' => 1,
            'title' => 'New Group',
            'description' => 'New Description',
            'organization' => 1,
            'created_at' => '2023-01-01T00:00:00Z',
            'updated_at' => '2023-01-01T00:00:00Z',
            'assets' => [],
            'asset_group_owners' => [],
            'asset_group_tags' => [],
            'suborganizations' => [],
            'is_dynamic' => false,
            'filters' => [],
            'criticality' => 1,
        ], 201),
    ]);

    $data = new CreateAssetGroupData(
        title: 'New Group',
        description: 'New Description',
        organization: 1,
        tags: [],
        owners: [],
        suborganizations: [],
        is_dynamic: false,
        criticality: ComplexityEnum::Low,
    );

    $assetGroup = LaravelPatrowl::createAssetGroup($data);

    Http::assertSent(function ($request) {
        return $request['organization'] === 1;
    });

    expect($assetGroup)
        ->toBeInstanceOf(AssetGroupData::class)
        ->id->toBe(1)
        ->title->toBe('New Group');
});

it('can create an asset group with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    Http::fake([
        '*/assets/group/' => Http::response([
            'id' => 1,
            'title' => 'New Group',
            'organization' => 456,
        ], 201),
    ]);

    $data = new CreateAssetGroupData(
        title: 'New Group',
        description: 'New Description',
    );

    LaravelPatrowl::createAssetGroup($data);

    Http::assertSent(function ($request) {
        return $request['organization'] === 456;
    });
});
