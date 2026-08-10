<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class ProductData
{
    public function __construct(
        public int $id,
        public string $name,
        public string $vendor
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            vendor: $data['vendor']
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
            'vendor' => $this->vendor,
        ];
    }
}
