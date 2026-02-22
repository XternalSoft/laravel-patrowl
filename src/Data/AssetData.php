<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\AssetOutsideBusinessHoursEnum;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Enums\ExposureEnum;
use Xternalsoft\LaravelPatrowl\Enums\LivenessEnum;
use Xternalsoft\LaravelPatrowl\Enums\TypeEf5Enum;

final class AssetData
{
    public function __construct(
        public int $id,
        public string $value,
        public ComplexityEnum $criticality,
        public TypeEf5Enum $type,
        public ?string $description,
        public ExposureEnum $exposure,
        public bool $is_active,
        public int $score,
        public ProtectionData $protection,
        public ?AssetOutsideBusinessHoursEnum $outside_business_hours,
        public string $created_by,
        public ?string $created_at,
        public ?string $updated_at,
        /** @var array<int> */
        public array $tags,
        public int $score_level,
        /** @var array<RelatedTechnologyData> */
        public ?array $technologies,
        /** @var array<AssetOwnerData> */
        public ?array $asset_owners,
        /** @var array<int> */
        public array $owners,
        /** @var array<AssetGroupLiteData> */
        public ?array $groups,
        public ?int $organization,
        /** @var array<AssetTagData> */
        public ?array $asset_tags,
        public ?string $provider,
        /** @var array<int> */
        public array $suborganizations,
        public ?string $monitored_slot_lock_until,
        public LivenessEnum $liveness,
        public ?DomainLiteData $www_related_domain,
        public bool $has_webservers,
        /** @var array<array> */
        public array $suborganizations_display,
        public mixed $ip_state
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'],
            criticality: ComplexityEnum::from($data['criticality']),
            type: TypeEf5Enum::from($data['type']),
            description: $data['description'] ?? null,
            exposure: ExposureEnum::from($data['exposure']),
            is_active: $data['is_active'],
            score: $data['score'],
            protection: ProtectionData::fromApi($data['protection']),
            outside_business_hours: isset($data['outside_business_hours']) ? AssetOutsideBusinessHoursEnum::from($data['outside_business_hours']) : null,
            created_by: $data['created_by'],
            created_at: $data['created_at'] ?? null,
            updated_at: $data['updated_at'] ?? null,
            tags: $data['tags'] ?? [],
            score_level: $data['score_level'],
            technologies: isset($data['technologies']) ? array_map(fn (array $tech) => RelatedTechnologyData::fromApi($tech), $data['technologies']) : null,
            asset_owners: isset($data['asset_owners']) ? array_map(fn (array $owner) => AssetOwnerData::fromApi($owner), $data['asset_owners']) : null,
            owners: $data['owners'] ?? [],
            groups: isset($data['groups']) ? array_map(fn (array $group) => AssetGroupLiteData::fromApi($group), $data['groups']) : null,
            organization: $data['organization'] ?? null,
            asset_tags: isset($data['asset_tags']) ? array_map(fn (array $tag) => AssetTagData::fromApi($tag), $data['asset_tags']) : null,
            provider: $data['provider'] ?? null,
            suborganizations: $data['suborganizations'] ?? [],
            monitored_slot_lock_until: $data['monitored_slot_lock_until'] ?? null,
            liveness: LivenessEnum::from($data['liveness']),
            www_related_domain: isset($data['www_related_domain']) ? DomainLiteData::fromApi($data['www_related_domain']) : null,
            has_webservers: $data['has_webservers'],
            suborganizations_display: $data['suborganizations_display'] ?? [],
            ip_state: $data['ip_state']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'criticality' => $this->criticality->value,
            'type' => $this->type->value,
            'description' => $this->description,
            'exposure' => $this->exposure->value,
            'is_active' => $this->is_active,
            'score' => $this->score,
            'protection' => $this->protection->toArray(),
            'outside_business_hours' => $this->outside_business_hours?->value,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'tags' => $this->tags,
            'score_level' => $this->score_level,
            'technologies' => $this->technologies ? array_map(fn (RelatedTechnologyData $tech) => $tech->toArray(), $this->technologies) : null,
            'asset_owners' => $this->asset_owners ? array_map(fn (AssetOwnerData $owner) => $owner->toArray(), $this->asset_owners) : null,
            'owners' => $this->owners,
            'groups' => $this->groups ? array_map(fn (AssetGroupLiteData $group) => $group->toArray(), $this->groups) : null,
            'organization' => $this->organization,
            'asset_tags' => $this->asset_tags ? array_map(fn (AssetTagData $tag) => $tag->toArray(), $this->asset_tags) : null,
            'provider' => $this->provider,
            'suborganizations' => $this->suborganizations,
            'monitored_slot_lock_until' => $this->monitored_slot_lock_until,
            'liveness' => $this->liveness->value,
            'www_related_domain' => $this->www_related_domain?->toArray(),
            'has_webservers' => $this->has_webservers,
            'suborganizations_display' => $this->suborganizations_display,
            'ip_state' => $this->ip_state,
        ];
    }
}
