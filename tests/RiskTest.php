<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Data\RiskData;
use Xternalsoft\LaravelPatrowl\Data\RiskInListData;
use Xternalsoft\LaravelPatrowl\Enums\RiskSeverityEnum;
use Xternalsoft\LaravelPatrowl\Enums\RiskStatusEnum;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Risks\ExportRisksCsvRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRiskRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRisksRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRiskSubtopicsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Risks\GetRiskTopicsRequest;
use Xternalsoft\LaravelPatrowl\Data\RiskTopicData;
use Xternalsoft\LaravelPatrowl\Data\RiskSubtopicData;

function getFakeRiskData(array $overrides = []): array
{
    return array_merge([
        'id' => 1,
        'title' => 'Sample Risk',
        'description' => 'A sample risk description',
        'status' => 'new',
        'severity' => 2,
        'type' => 'vuln',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-01T00:00:00Z',
    ], $overrides);
}

it('handles integer status and severity from api', function () {
    $data = [
        'id' => 1,
        'title' => 'Sample Risk',
        'status' => 0, // Should map to 'new' -> RiskStatusEnum::New
        'severity' => 4, // Should map to RiskSeverityEnum::Critical
        'asset' => 123,
    ];

    $risk = RiskInListData::fromApi($data);

    expect($risk->status)->toBe(RiskStatusEnum::New)
        ->and($risk->severity)->toBe(RiskSeverityEnum::Critical);
});

it('handles nested objects for asset topic and subtopic', function () {
    $data = [
        'id' => 1,
        'title' => 'Sample Risk',
        'asset' => [
            'id' => 123,
            'value' => 'example.com',
        ],
        'topic' => [
            'id' => 10,
            'title' => 'Security',
            'slug' => 'security',
        ],
        'subtopic' => [
            'id' => 20,
            'title' => 'Network',
            'slug' => 'network',
        ],
    ];

    $risk = RiskInListData::fromApi($data);

    expect($risk->assetId)->toBe(123)
        ->and($risk->assetValue)->toBe('example.com')
        ->and($risk->topic)->toBe('Security')
        ->and($risk->topicId)->toBe(10)
        ->and($risk->topicSlug)->toBe('security')
        ->and($risk->subtopic)->toBe('Network')
        ->and($risk->subtopicId)->toBe(20)
        ->and($risk->subtopicSlug)->toBe('network');
});

it('parses pipe separated asset tags', function () {
    $data = [
        'id' => 1,
        'title' => 'Sample Risk',
        'asset_tags' => 'tag1|tag2||tag3',
    ];

    $risk = RiskInListData::fromApi($data);

    expect($risk->assetTags)->toBe(['tag1', 'tag2', 'tag3']);
});

it('maps description and raw_data correctly', function () {
    // Case 1: description exists
    $risk1 = RiskInListData::fromApi(['description' => 'Desc 1']);
    expect($risk1->description)->toBe('Desc 1');

    // Case 2: only raw_data exists
    $risk2 = RiskInListData::fromApi(['raw_data' => 'Raw 2']);
    expect($risk2->description)->toBe('Raw 2');
});

it('can get risks', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetRisksRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                getFakeRiskData(),
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $risks = iterator_to_array(LaravelPatrowl::risks()->all()->items());

    expect($risks)->toHaveCount(1)
        ->and($risks[0])->toBeInstanceOf(RiskInListData::class)
        ->and($risks[0]->id)->toBe(1)
        ->and($risks[0]->title)->toBe('Sample Risk')
        ->and($risks[0]->severity)->toBe(RiskSeverityEnum::Medium)
        ->and($risks[0]->status)->toBe(RiskStatusEnum::New);
});

it('can get a specific risk', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetRiskRequest::class => MockResponse::make(getFakeRiskData(), 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $risk = LaravelPatrowl::risks()->get(1);

    expect($risk)->toBeInstanceOf(RiskData::class)
        ->id->toBe(1)
        ->title->toBe('Sample Risk')
        ->severity->toBe(RiskSeverityEnum::Medium)
        ->status->toBe(RiskStatusEnum::New);
});

it('can export risks to csv', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        ExportRisksCsvRequest::class => MockResponse::make('id,title,severity', 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $csv = LaravelPatrowl::risks()->exportCsv();

    expect($csv)->toBe('id,title,severity');
});

it('can get risk topics', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetRiskTopicsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                ['id' => 1, 'title' => 'Security', 'slug' => 'security'],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $topics = iterator_to_array(LaravelPatrowl::risks()->topics()->items());

    expect($topics)->toHaveCount(1)
        ->and($topics[0])->toBeInstanceOf(RiskTopicData::class)
        ->and($topics[0]->id)->toBe(1)
        ->and($topics[0]->title)->toBe('Security');
});

it('can get risk subtopics', function () {
    config()->set('patrowl.api_token', 'fake-token');

    $mockClient = new MockClient([
        GetRiskSubtopicsRequest::class => MockResponse::make([
            'count' => 1,
            'next' => null,
            'previous' => null,
            'results' => [
                [
                    'id' => 75,
                    'title' => 'Weak ciphersuite',
                    'slug' => 'weak-ciphersuite',
                    'description' => 'Test description',
                    'is_available' => true,
                    'default_severity' => 1,
                    'security_check' => 31,
                    'remediation' => 'Test remediation',
                    'remediation_effort' => 0,
                    'remediation_priority' => 0,
                ],
            ],
        ], 200),
    ]);

    LaravelPatrowl::withMockClient($mockClient);

    $subtopics = iterator_to_array(LaravelPatrowl::risks()->subtopics()->items());

    expect($subtopics)->toHaveCount(1)
        ->and($subtopics[0])->toBeInstanceOf(RiskSubtopicData::class)
        ->and($subtopics[0]->id)->toBe(75)
        ->and($subtopics[0]->title)->toBe('Weak ciphersuite')
        ->and($subtopics[0]->description)->toBe('Test description')
        ->and($subtopics[0]->isAvailable)->toBeTrue()
        ->and($subtopics[0]->securityCheck)->toBe(31);
});
