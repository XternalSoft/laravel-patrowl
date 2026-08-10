<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class TechnologyAggregateData
{
    public function __construct(
        public int $id,
        public int $assets,
        public int $cves,
        public ?string $lastSeenAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            assets: $data['assets'],
            cves: $data['cves'],
            lastSeenAt: $data['last_seen_at'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'assets' => $this->assets,
            'cves' => $this->cves,
            'last_seen_at' => $this->lastSeenAt,
        ];
    }
}
