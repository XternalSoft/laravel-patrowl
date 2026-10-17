<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data\Easm;

/**
 * A technology affected by a CVE.
 */
final class TechnologyInCveData
{
    public function __construct(
        public int $id,
        public ?string $product = null,
        public ?string $version = null,
        public ?string $vendor = null,
        public ?string $partialCpe = null
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
            vendor: $data['vendor'] ?? null,
            partialCpe: $data['partial_cpe'] ?? null
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
            'vendor' => $this->vendor,
            'partial_cpe' => $this->partialCpe,
        ];
    }
}
