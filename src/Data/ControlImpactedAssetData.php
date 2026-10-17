<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

/**
 * An asset impacted (or potentially impacted) by a control.
 */
final class ControlImpactedAssetData
{
    public function __construct(
        public int $id,
        public string $value,
        public ?string $type = null,
        public ?int $criticality = null,
        public ?float $scoreLevel = null,
        public ?string $controlImpact = null,
        public ?ProtectionData $protection = null,
        public ?int $outsideBusinessHours = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            value: (string) $data['value'],
            type: $data['type'] ?? null,
            criticality: $data['criticality'] ?? null,
            scoreLevel: isset($data['score_level']) ? (float) $data['score_level'] : null,
            controlImpact: $data['control_impact'] ?? null,
            protection: isset($data['protection']) ? ProtectionData::fromApi($data['protection']) : null,
            outsideBusinessHours: $data['outside_business_hours'] ?? null
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
            'type' => $this->type,
            'criticality' => $this->criticality,
            'score_level' => $this->scoreLevel,
            'control_impact' => $this->controlImpact,
            'protection' => $this->protection?->toArray(),
            'outside_business_hours' => $this->outsideBusinessHours,
        ];
    }
}
