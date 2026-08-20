<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationIdentityData
{
    public function __construct(
        public int $id,
        public int $userId,
        public string $name,
        public string $slug,
        public bool $isActive
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            userId: (int) ($data['user_id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            slug: (string) ($data['slug'] ?? ''),
            isActive: (bool) ($data['is_active'] ?? false)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => $this->isActive,
        ];
    }
}
