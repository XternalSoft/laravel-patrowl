<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * The product of a technology.
 */
final class ProductInTechnologyData
{
    public function __construct(
        public int $id,
        public ?string $name = null,
        public ?VendorInProductData $vendor = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'] ?? null,
            vendor: isset($data['vendor']) ? VendorInProductData::fromApi($data['vendor']) : null
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
            'vendor' => $this->vendor?->toArray(),
        ];
    }
}
