<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class TagData
{
    /**
     * @param  array<int, array<string, mixed>>  $assets
     * @param  array<int, array<string, mixed>>  $assetGroups
     * @param  array<string, mixed>|null  $createdBy
     */
    public function __construct(
        public int $id,
        public string $value,
        public ?string $description = null,
        public ?int $organization = null,
        public array $assets = [],
        public array $assetGroups = [],
        public ?array $createdBy = null,
        public ?string $createdAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'],
            description: $data['description'] ?? null,
            organization: $data['organization'] ?? null,
            assets: $data['assets'] ?? [],
            assetGroups: $data['asset_groups'] ?? [],
            createdBy: $data['created_by'] ?? null,
            createdAt: $data['created_at'] ?? null
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
            'description' => $this->description,
            'organization' => $this->organization,
            'assets' => $this->assets,
            'asset_groups' => $this->assetGroups,
            'created_by' => $this->createdBy,
            'created_at' => $this->createdAt,
        ];
    }
}
