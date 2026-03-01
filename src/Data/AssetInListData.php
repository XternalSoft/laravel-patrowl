<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\AssetOutsideBusinessHoursEnum;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Enums\ExposureEnum;
use Xternalsoft\LaravelPatrowl\Enums\LivenessEnum;
use Xternalsoft\LaravelPatrowl\Enums\TypeEf5Enum;

/**
 * Represents an Asset in a list view from the Patrowl API, based on example response.
 */
final class AssetInListData
{
    public function __construct(
        public ?int $id = null,
        public ?string $value = null,
        public ?ComplexityEnum $criticality = null,
        public ?TypeEf5Enum $type = null,
        public ?string $description = null,
        public ?ExposureEnum $exposure = null,
        public ?bool $isActive = null,
        public ?int $score = null,
        public ?ProtectionData $protection = null,
        public ?string $createdBy = null,
        public ?int $activevulns = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        /** @var array<int, mixed>|null */
        public ?array $assetTags = null,
        public ?int $organization = null,
        public ?float $scoreLevel = null,
        /** @var array<int, mixed>|null */
        public ?array $assetOwners = null,
        public ?LivenessEnum $liveness = null,
        public ?AssetOutsideBusinessHoursEnum $outsideBusinessHours = null,
        public ?bool $monitoredSlotLocked = null,
        public ?string $ipType = null,
        public ?RelatedTechnologyData $relatedTechnologies = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            value: $data['value'] ?? null,
            criticality: isset($data['criticality']) ? ComplexityEnum::from($data['criticality']) : null,
            type: isset($data['type']) ? TypeEf5Enum::from($data['type']) : null,
            description: $data['description'] ?? null,
            exposure: isset($data['exposure']) ? ExposureEnum::from($data['exposure']) : null,
            isActive: $data['is_active'] ?? null,
            score: $data['score'] ?? null,
            protection: isset($data['protection']) ? ProtectionData::fromApi($data['protection']) : null,
            createdBy: $data['created_by'] ?? null,
            activevulns: $data['activevulns'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            assetTags: $data['asset_tags'] ?? null,
            organization: $data['organization'] ?? null,
            scoreLevel: $data['score_level'] ?? null,
            assetOwners: $data['asset_owners'] ?? null,
            liveness: isset($data['liveness']) ? LivenessEnum::from($data['liveness']) : null,
            outsideBusinessHours: isset($data['outside_business_hours']) ? AssetOutsideBusinessHoursEnum::from($data['outside_business_hours']) : null,
            monitoredSlotLocked: $data['monitored_slot_locked'] ?? null,
            ipType: $data['ip_type'] ?? null,
            relatedTechnologies: isset($data['related_technologies']) ? RelatedTechnologyData::fromApi($data['related_technologies']) : null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'criticality' => $this->criticality?->value,
            'type' => $this->type?->value,
            'description' => $this->description,
            'exposure' => $this->exposure?->value,
            'is_active' => $this->isActive,
            'score' => $this->score,
            'protection' => $this->protection?->toArray(),
            'created_by' => $this->createdBy,
            'activevulns' => $this->activevulns,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'asset_tags' => $this->assetTags,
            'organization' => $this->organization,
            'score_level' => $this->scoreLevel,
            'asset_owners' => $this->assetOwners,
            'liveness' => $this->liveness?->value,
            'outside_business_hours' => $this->outsideBusinessHours?->value,
            'monitored_slot_locked' => $this->monitoredSlotLocked,
            'ip_type' => $this->ipType,
            'related_technologies' => $this->relatedTechnologies?->toArray(),
        ];
    }
}
