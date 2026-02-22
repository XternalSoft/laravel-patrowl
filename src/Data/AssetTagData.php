<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetTagData
{
    public function __construct(
        public int $id,
        public string $value,
        public int $organization
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            value: $data['value'],
            organization: $data['organization']
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'value' => $this->value,
            'organization' => $this->organization,
        ];
    }
}
