<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupInListData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\AddAssetsToGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\AddTagToAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\CreateAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\GetAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\GetAssetGroupsRequest;

it('can get asset groups', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetAssetGroupsRequest::class => MockResponse::make([
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

    LaravelPatrowl::withMockClient($mockClient);

    $assetGroups = iterator_to_array(LaravelPatrowl::assetGroups()->all()->items());

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

    $mockClient = new MockClient([
        GetAssetGroupsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::assetGroups()->all()->items());

    $mockClient->assertSent(function (GetAssetGroupsRequest $request) {
        return $request->query()->all() === ['org_id' => 456, 'limit' => 100, 'page' => 1];
    });
});

it('can create an asset group', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        CreateAssetGroupRequest::class => MockResponse::make([
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

    LaravelPatrowl::withMockClient($mockClient);

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

    $assetGroup = LaravelPatrowl::assetGroups()->create($data);

    $mockClient->assertSent(function (CreateAssetGroupRequest $request) {
        return $request->body()->all()['organization'] === 1;
    });

    expect($assetGroup)
        ->toBeInstanceOf(AssetGroupData::class)
        ->id->toBe(1)
        ->title->toBe('New Group');
});

it('can create an asset group with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        CreateAssetGroupRequest::class => MockResponse::make([
            'id' => 1,
            'title' => 'New Group',
            'organization' => 456,
        ], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetGroupData(
        title: 'New Group',
        description: 'New Description',
    );

    LaravelPatrowl::assetGroups()->create($data);

    $mockClient->assertSent(function (CreateAssetGroupRequest $request) {
        return $request->body()->all()['organization'] === 456;
    });
});

it('can create an asset group with assets', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        CreateAssetGroupRequest::class => MockResponse::make([
            'id' => 1,
            'title' => 'Group with Assets',
        ], 201),
        AddAssetsToGroupRequest::class => MockResponse::make([], 200),
        GetAssetGroupRequest::class => MockResponse::make([
            'id' => 1,
            'title' => 'Group with Assets',
            'assets' => [['id' => 1], ['id' => 2]],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new CreateAssetGroupData(
        title: 'Group with Assets',
        assets: [1, 2]
    );

    $group = LaravelPatrowl::assetGroups()->create($data);

    $mockClient->assertSent(CreateAssetGroupRequest::class);
    $mockClient->assertSent(function ($request) {
        return $request instanceof AddAssetsToGroupRequest &&
               $request->body()->all()['assets_id'] === [1, 2];
    });
    $mockClient->assertSent(GetAssetGroupRequest::class);

    expect($group->assets)->toHaveCount(2);
});

it('can add a tag to an asset group', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        AddTagToAssetGroupRequest::class => MockResponse::make([], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new AddTagToAssetData(
        value: 'group-tag',
        organization: 1
    );

    $response = LaravelPatrowl::assetGroups()->addTag(1, $data);

    $mockClient->assertSent(function (AddTagToAssetGroupRequest $request) {
        return $request->resolveEndpoint() === '/assets/group/1/tag' &&
               $request->body()->all() === ['value' => 'group-tag', 'organization' => 1];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(201);
});

it('can add a tag to an asset group with default organization id', function () {
    config()->set('patrowl.api_token', 'fake-token');
    config()->set('patrowl.default_organization_id', 456);

    $mockClient = new MockClient([
        AddTagToAssetGroupRequest::class => MockResponse::make([], 201),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $data = new AddTagToAssetData(
        value: 'group-tag'
    );

    $response = LaravelPatrowl::assetGroups()->addTag(1, $data);

    $mockClient->assertSent(function (AddTagToAssetGroupRequest $request) {
        return $request->body()->all() === ['value' => 'group-tag', 'organization' => 456];
    });

    expect($response)->toBeInstanceOf(Response::class);
    expect($response->status())->toBe(201);
});
