<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class ProtectionData
{
    public function __construct(
        public ?string $status = null,
        public ?string $availability = null
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            status: $data['status'] ?? null,
            availability: $data['availability'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'availability' => $this->availability,
        ];
    }
}
