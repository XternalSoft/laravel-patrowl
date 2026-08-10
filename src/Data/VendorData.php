<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class VendorData
{
    public function __construct(
        public int $id,
        public string $name
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name']
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
        ];
    }
}
