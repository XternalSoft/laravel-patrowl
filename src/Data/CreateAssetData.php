<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Enums\ExposureEnum;

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
        public ?array $teams = null
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'organization' => $this->organization,
            'value' => $this->value,
            'description' => $this->description,
            'criticality' => $this->criticality?->value,
            'exposure' => $this->exposure?->value,
            'tags' => $this->tags,
            'owners' => $this->owners,
            'teams' => $this->teams,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
