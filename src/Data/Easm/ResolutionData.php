<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A link between a domain and an IPv4 address it resolves to.
 */
final class ResolutionData
{
    public function __construct(
        public int $id,
        /** @var array{id: int, value: string}|null */
        public ?array $domain = null,
        /** @var array{id: int, value: string, type: string}|null */
        public ?array $ipv4 = null,
        public ?string $firstResolvedAt = null,
        public ?string $lastResolvedAt = null,
        public ?bool $resolves = null,
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
            domain: $data['domain'] ?? null,
            ipv4: $data['ipv4'] ?? null,
            firstResolvedAt: $data['first_resolved_at'] ?? null,
            lastResolvedAt: $data['last_resolved_at'] ?? null,
            resolves: $data['resolves'] ?? null,
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
            'domain' => $this->domain,
            'ipv4' => $this->ipv4,
            'first_resolved_at' => $this->firstResolvedAt,
            'last_resolved_at' => $this->lastResolvedAt,
            'resolves' => $this->resolves,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
