<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A single detection of a technology on an asset/service.
 */
final class TechnologyDetectionData
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        public ?string $assetType = null,
        public ?TechnologyData $technology = null,
        public ?ServiceInTechnologyDetectionData $service = null,
        public ?string $source = null,
        public ?int $confidence = null,
        public ?bool $isRelevant = null,
        public ?string $firstSeenAt = null,
        public ?string $lastSeenAt = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'] ?? null,
            assetType: $data['asset_type'] ?? null,
            technology: isset($data['technology']) ? TechnologyData::fromApi($data['technology']) : null,
            service: isset($data['service']) ? ServiceInTechnologyDetectionData::fromApi($data['service']) : null,
            source: $data['source'] ?? null,
            confidence: $data['confidence'] ?? null,
            isRelevant: $data['is_relevant'] ?? null,
            firstSeenAt: $data['first_seen_at'] ?? null,
            lastSeenAt: $data['last_seen_at'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
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
            'asset_type' => $this->assetType,
            'technology' => $this->technology?->toArray(),
            'service' => $this->service?->toArray(),
            'source' => $this->source,
            'confidence' => $this->confidence,
            'is_relevant' => $this->isRelevant,
            'first_seen_at' => $this->firstSeenAt,
            'last_seen_at' => $this->lastSeenAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
