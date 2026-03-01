<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetGroupData
{
    public function __construct(
        public string $title,
        public ?int $id = null,
        public ?string $description = null,
        public string $scope = 'private',
        /** @var array<int, mixed> */
        public array $tags = [],
        /** @var array<int, mixed> */
        public array $assets = [],
        /** @var array<int, mixed> */
        public array $owners = [],
        public ?int $assetsCount = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?int $todosCount = null,
        public ?int $commentsCount = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            title: $data['title'],
            id: $data['id'] ?? null,
            description: $data['description'] ?? null,
            scope: $data['scope'] ?? 'private',
            tags: $data['tags'] ?? [],
            assets: $data['assets'] ?? [],
            owners: $data['owners'] ?? [],
            assetsCount: $data['assets_count'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            todosCount: $data['todos_count'] ?? null,
            commentsCount: $data['comments_count'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'id' => $this->id,
            'description' => $this->description,
            'scope' => $this->scope,
            'tags' => $this->tags,
            'assets' => $this->assets,
            'owners' => $this->owners,
            'assets_count' => $this->assetsCount,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'todos_count' => $this->todosCount,
            'comments_count' => $this->commentsCount,
        ];
    }
}
