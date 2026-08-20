<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\CreditsData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationCreditsData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationFeaturesData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationIdentityData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationMembersData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationMonitoringData;
use Xternalsoft\LaravelPatrowl\Data\OrganizationProfileData;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetDefaultOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationRequest;
use Xternalsoft\LaravelPatrowl\Requests\Organizations\GetOrganizationsRequest;

$mockOrgResponseData = [
    'identity' => [
        'id' => 42,
        'user_id' => 100,
        'name' => 'My Organization',
        'slug' => 'my-organization',
        'is_active' => true,
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

it('can get organizations list', function () use ($mockOrgResponseData) {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetOrganizationsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                $mockOrgResponseData,
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $orgs = iterator_to_array(LaravelPatrowl::organizations()->all()->items());

    expect($orgs)->toHaveCount(1);

    $org = $orgs[0];
    expect($org)->toBeInstanceOf(OrganizationData::class);

    // Assert Identity
    expect($org->identity)->toBeInstanceOf(OrganizationIdentityData::class)
        ->id->toBe(42)
        ->userId->toBe(100)
        ->name->toBe('My Organization')
        ->slug->toBe('my-organization')
        ->isActive->toBeTrue();

    // Assert Members
    expect($org->members)->toBeInstanceOf(OrganizationMembersData::class)
        ->owner->toBe('gparrot@xternalsoft.com')
        ->userCount->toBe(5);

    // Assert Profile
    expect($org->profile)->toBeInstanceOf(OrganizationProfileData::class)
        ->isPoc->toBeFalse()
        ->createdAt->toBe('2026-06-04T11:00:00Z')
        ->endOfContract->toBe('2027-06-04T11:00:00Z');

    // Assert Features
    expect($org->features)->toBeInstanceOf(OrganizationFeaturesData::class)
        ->isSso->toBeTrue()
        ->riskInsightsEnabled->toBeTrue()
        ->teamsEnabled->toBeTrue()
        ->technologiesEnabled->toBeTrue()
        ->typosquattingEnabled->toBeTrue()
        ->outsideBusinessHoursEnabled->toBeTrue()
        ->outsideBusinessHours->toBeTrue()
        ->autoEasm->toBeTrue();

    // Assert Credits
    expect($org->credits)->toBeInstanceOf(OrganizationCreditsData::class)
        ->greybox->toBe(2);
    expect($org->credits->easm)->toBeInstanceOf(CreditsData::class)
        ->available->toBe(10)
        ->limit->toBe(20);
    expect($org->credits->pentest)->toBeInstanceOf(CreditsData::class)
        ->available->toBe(5)
        ->limit->toBe(10);

    // Assert Monitoring
    expect($org->monitoring)->toBeInstanceOf(OrganizationMonitoringData::class)
        ->pentestedAssets->toBe(12)
        ->rotationFrequency->toBe(30)
        ->autoEasmActivationConsumption->toBe(1);

    // Assert toArray
    expect($org->toArray())->toBeArray()
        ->toHaveKey('identity')
        ->toHaveKey('members')
        ->toHaveKey('profile')
        ->toHaveKey('features')
        ->toHaveKey('credits')
        ->toHaveKey('monitoring');
});

it('can filter organizations list with query parameters', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetOrganizationsRequest::class => MockResponse::make([
            'count' => 0,
            'next' => null,
            'previous' => null,
            'results' => [],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    iterator_to_array(LaravelPatrowl::organizations()->all([
        'search' => 'My Org',
        'owner' => 'gparrot@xternalsoft.com',
    ])->items());

    $mockClient->assertSent(function (GetOrganizationsRequest $request) {
        return $request->query()->all() === [
            'search' => 'My Org',
            'owner' => 'gparrot@xternalsoft.com',
            'limit' => 100,
            'page' => 1,
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
});

it('can get default organization in detail', function () use ($mockOrgResponseData) {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetDefaultOrganizationRequest::class => MockResponse::make($mockOrgResponseData, 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $org = LaravelPatrowl::organizations()->default();

    expect($org)->toBeInstanceOf(OrganizationData::class);
    expect($org->identity->id)->toBe(42);
    expect($org->identity->name)->toBe('My Organization');
});
