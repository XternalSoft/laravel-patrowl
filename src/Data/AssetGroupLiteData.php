<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetGroupLiteData
{
    public function __construct(
        public int $id,
        public ?string $title = null,
        public ?string $description = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'] ?? null,
            description: $data['description'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
        ];
    }
}
