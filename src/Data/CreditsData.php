<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class CreditsData
{
    public function __construct(
        public int $available,
        public int $limit
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            available: (int) ($data['available'] ?? 0),
            limit: (int) ($data['limit'] ?? 0)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'available' => $this->available,
            'limit' => $this->limit,
        ];
    }
}
