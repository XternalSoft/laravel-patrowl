<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetTagPolicyData
{
    public function __construct(
        public string $name,
        /** @var array<int, mixed> */
        public array $tags,
        public ?int $id = null,
        public ?string $description = null,
        public string $scope = 'private',
        /** @var array<int, mixed> */
        public array $filters = [],
        public bool $isActive = true,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            name: $data['name'],
            tags: $data['tags'],
            id: $data['id'] ?? null,
            description: $data['description'] ?? null,
            scope: $data['scope'] ?? 'private',
            filters: $data['filters'] ?? [],
            isActive: $data['is_active'] ?? true,
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
            'name' => $this->name,
            'tags' => $this->tags,
            'id' => $this->id,
            'description' => $this->description,
            'scope' => $this->scope,
            'filters' => $this->filters,
            'is_active' => $this->isActive,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
