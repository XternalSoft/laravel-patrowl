<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * An autonomous system associated with the IPv4 addresses of assets.
 */
final class AsnData
{
    public function __construct(
        public int $id,
        public ?string $number = null,
        public ?string $cidr = null,
        public ?string $registry = null,
        public ?string $description = null,
        public ?string $countryCode = null,
        public ?int $ipv4Count = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            number: $data['number'] ?? null,
            cidr: $data['cidr'] ?? null,
            registry: $data['registry'] ?? null,
            description: $data['description'] ?? null,
            countryCode: $data['country_code'] ?? null,
            ipv4Count: $data['ipv4_count'] ?? null,
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
            'id' => $this->id,
            'number' => $this->number,
            'cidr' => $this->cidr,
            'registry' => $this->registry,
            'description' => $this->description,
            'country_code' => $this->countryCode,
            'ipv4_count' => $this->ipv4Count,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
