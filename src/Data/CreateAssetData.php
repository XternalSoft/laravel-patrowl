<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Enums\ExposureEnum;

use function config;

final class CreateAssetData
{
    public function __construct(
        public string $value,
        public ?int $organization = null,
        public ?string $description = null,
        public ?ComplexityEnum $criticality = null,
        public ?ExposureEnum $exposure = null,
        /** @var int[]|null */
        public ?array $tags = null,
        /** @var int[]|null */
        public ?array $owners = null,
        /** @var int[]|null */
        public ?array $suborganizations = null
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'org_id' => $this->organization ?? config('patrowl.default_organization_id'),
            'value' => $this->value,
            'description' => $this->description,
            'criticality' => $this->criticality?->value,
            'exposure' => $this->exposure?->value,
            'tags' => $this->tags,
            'owners' => $this->owners,
            'suborganizations' => $this->suborganizations,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
