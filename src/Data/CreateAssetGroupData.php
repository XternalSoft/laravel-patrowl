<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;

final class CreateAssetGroupData
{
    public function __construct(
        public string $title,
        public ?string $description = null,
        public ?int $organization = null,
        /** @var array<int, mixed> */
        public array $tags = [],
        /** @var array<int, mixed> */
        public array $owners = [],
        /** @var array<int, mixed> */
        public array $suborganizations = [],
        public bool $is_dynamic = false,
        public ComplexityEnum $criticality = ComplexityEnum::Medium,
        /** @var array<int, mixed> */
        public array $assets = [],
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'description' => $this->description,
            'organization' => $this->organization,
            'tags' => $this->tags,
            'owners' => $this->owners,
            'suborganizations' => $this->suborganizations,
            'is_dynamic' => $this->is_dynamic,
            'criticality' => $this->criticality->value,
            'assets_id' => $this->assets,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
