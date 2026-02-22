<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\AssetOutsideBusinessHoursEnum;

final class DomainLiteData
{
    public function __construct(
        public int $id,
        public string $value,
        public ProtectionData $protection,
        public AssetOutsideBusinessHoursEnum $outside_business_hours
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'],
            protection: ProtectionData::fromApi($data['protection']),
            outside_business_hours: AssetOutsideBusinessHoursEnum::from($data['outside_business_hours'])
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'protection' => $this->protection->toArray(),
            'outside_business_hours' => $this->outside_business_hours->value,
        ];
    }
}
