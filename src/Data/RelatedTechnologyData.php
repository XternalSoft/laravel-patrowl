<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class RelatedTechnologyData
{
    public function __construct(
        public ?int $id = null,
        public ?string $product = null,
        public ?string $vendor = null,
        public ?string $version = null,
        public ?bool $impactedByCve = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            product: $data['product'] ?? null,
            vendor: $data['vendor'] ?? null,
            version: $data['version'] ?? null,
            impactedByCve: $data['impacted_by_cve'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'product' => $this->product,
            'vendor' => $this->vendor,
            'version' => $this->version,
            'impacted_by_cve' => $this->impactedByCve,
        ];
    }
}
