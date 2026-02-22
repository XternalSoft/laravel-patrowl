<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetTagPolicyFilterData
{
    public function __construct(
        public string $field,
        public string $operator,
        public string $value,
        public ?int $id = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            field: $data['field'],
            operator: $data['operator'],
            value: $data['value'],
            id: $data['id'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'field' => $this->field,
            'operator' => $this->operator,
            'value' => $this->value,
            'id' => $this->id,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
