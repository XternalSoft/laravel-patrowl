<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class ControlAssetData
{
    public function __construct(
        public int $id,
        public string $value
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: (int) $data['id'],
            value: (string) $data['value']
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
        ];
    }
}
