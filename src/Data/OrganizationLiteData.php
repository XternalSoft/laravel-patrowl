<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationLiteData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $tenantId,
        public string $tenantName
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            tenantId: $data['tenant_id'],
            tenantName: $data['tenant_name']
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'tenant_id' => $this->tenantId,
            'tenant_name' => $this->tenantName,
        ];
    }
}
