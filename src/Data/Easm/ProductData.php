<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A product (application, operating system, hardware) of a vendor (global catalog).
 */
final class ProductData
{
    public function __construct(
        public int $id,
        /** @var array{id: int, name: string}|null */
        public ?array $vendor = null,
        public ?string $name = null,
        public ?string $type = null,
        public ?string $partialCpe = null,
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
            vendor: $data['vendor'] ?? null,
            name: $data['name'] ?? null,
            type: $data['type'] ?? null,
            partialCpe: $data['partial_cpe'] ?? null,
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
            'vendor' => $this->vendor,
            'name' => $this->name,
            'type' => $this->type,
            'partial_cpe' => $this->partialCpe,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
