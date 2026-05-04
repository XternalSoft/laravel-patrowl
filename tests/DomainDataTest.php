<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Tests;

use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\DomainData;

it('can instantiate DomainData from api data', function () {
    $data = [
        'id' => 1,
        'value' => 'domain.com',
        'criticality' => 1,
        'type' => 'domain',
        'description' => 'A domain',
        'exposure' => 'external',
        'is_active' => true,
        'score' => 10,
        'protection' => ['status' => 'unprotected', 'availability' => 'available'],
        'outside_business_hours' => 0,
        'created_by' => 'admin',
        'created_at' => '2023-01-01T00:00:00Z',
        'updated_at' => '2023-01-01T00:00:00Z',
        'score_level' => 1,
        'technologies' => [],
        'asset_owners' => [],
        'owners' => [],
        'groups' => [],
        'organization' => 1,
        'asset_tags' => [],
        'provider' => 'aws',
        'suborganizations' => [],
        'monitored_slot_lock_until' => null,
        'liveness' => 'up',
        'www_related_domain' => null,
        'has_webservers' => false,
        'suborganizations_display' => [],
        'ip_state' => 'running',
    ];

    $domain = DomainData::fromApi($data);

    expect($domain)->toBeInstanceOf(DomainData::class)
        ->and($domain)->toBeInstanceOf(AssetData::class)
        ->and($domain->id)->toBe(1)
        ->and($domain->value)->toBe('domain.com');
});
