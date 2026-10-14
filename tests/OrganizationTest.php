<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\OrganizationData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationLiteData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationsRequest;

$mockOrgResponseData = [
    'identity' => [
        'id' => 42,
        'user_id' => 100,
        'name' => 'My Organization',
        'slug' => 'my-organization',
        'is_active' => true,
        'tenant_id' => 7,
    ],
    'members' => [
        'owner' => 'gparrot@xternalsoft.com',
        'user_count' => 5,
    ],
    'profile' => [
        'is_poc' => false,
        'created_at' => '2026-06-04T11:00:00Z',
        'end_of_contract' => '2027-06-04T11:00:00Z',
    ],
    'features' => [
        'is_sso' => true,
        'risk_insights_enabled' => true,
        'teams_enabled' => true,
        'technologies_enabled' => true,
        'typosquatting_enabled' => true,
        'outside_business_hours_enabled' => true,
        'outside_business_hours' => true,
        'auto_easm' => true,
    ],
    'credits' => [
        'easm' => [
            'available' => 10,
            'limit' => 20,
        ],
        'pentest' => [
            'available' => 5,
            'limit' => 10,
        ],
        'greybox' => 2,
    ],
    'monitoring' => [
        'pentested_assets' => 12,
        'rotation_frequency' => 30,
        'auto_easm_activation_consumption' => 1,
    ],
];

it('can get organizations list', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetOrganizationsRequest::class => MockResponse::make([
            ['id' => 42, 'name' => 'My Organization', 'slug' => 'my-organization', 'tenant_id' => 7, 'tenant_name' => 'Acme'],
            ['id' => 43, 'name' => 'Other', 'slug' => 'other', 'tenant_id' => 7, 'tenant_name' => 'Acme'],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $orgs = LaravelPatrowl::organizations()->all();

    expect($orgs)->toHaveCount(2)
        ->and($orgs[0])->toBeInstanceOf(OrganizationLiteData::class)
        ->id->toBe(42)
        ->name->toBe('My Organization')
        ->slug->toBe('my-organization')
        ->tenantId->toBe(7)
        ->tenantName->toBe('Acme')
        ->and($orgs[0]->toArray())->toBe([
            'id' => 42,
            'name' => 'My Organization',
            'slug' => 'my-organization',
            'tenant_id' => 7,
            'tenant_name' => 'Acme',
        ]);
});

it('can filter organizations list with query parameters', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetOrganizationsRequest::class => MockResponse::make([], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    LaravelPatrowl::organizations()->all([
        'search' => 'My Org',
        'owner' => 'gparrot@xternalsoft.com',
    ]);

    $mockClient->assertSent(function (GetOrganizationsRequest $request) {
        return $request->query()->all() === [
            'search' => 'My Org',
            'owner' => 'gparrot@xternalsoft.com',
        ];
    });
});

it('can get a specific organization in detail', function () use ($mockOrgResponseData) {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetOrganizationRequest::class => MockResponse::make($mockOrgResponseData, 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $org = LaravelPatrowl::organizations()->get(42);

    expect($org)->toBeInstanceOf(OrganizationData::class);
    expect($org->identity->id)->toBe(42);
    expect($org->identity->name)->toBe('My Organization');
    expect($org->identity->tenantId)->toBe(7);
});
