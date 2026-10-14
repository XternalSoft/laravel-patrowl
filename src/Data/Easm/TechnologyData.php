<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A distinct product/version combination detected on assets.
 */
final class TechnologyData
{
    public function __construct(
        public int $id,
        /** @var array{id: int, name: string, vendor: array{id: int, name: string}}|null */
        public ?array $product = null,
        public ?string $version = null,
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
            product: $data['product'] ?? null,
            version: $data['version'] ?? null,
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
            'product' => $this->product,
            'version' => $this->version,
            'partial_cpe' => $this->partialCpe,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
