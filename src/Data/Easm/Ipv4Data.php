<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * An IPv4 address owned by an asset or contextualized (resolved from a domain).
 */
final class Ipv4Data
{
    public function __construct(
        public int $id,
        public ?string $value = null,
        public ?int $asn = null,
        public ?string $type = null,
        public ?bool $inBlocklist = null,
        public mixed $blocklists = null,
        public ?Ipv4ProviderData $provider = null,
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
            value: $data['value'] ?? null,
            asn: $data['asn'] ?? null,
            type: $data['type'] ?? null,
            inBlocklist: $data['in_blocklist'] ?? null,
            blocklists: $data['blocklists'] ?? null,
            provider: isset($data['provider']) ? Ipv4ProviderData::fromApi($data['provider']) : null,
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
            'value' => $this->value,
            'asn' => $this->asn,
            'type' => $this->type,
            'in_blocklist' => $this->inBlocklist,
            'blocklists' => $this->blocklists,
            'provider' => $this->provider?->toArray(),
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
