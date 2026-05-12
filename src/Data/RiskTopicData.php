<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final readonly class RiskTopicData
{
    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            id: $data['id'],
            title: $data['title'] ?? $data['name'] ?? '',
            slug: $data['slug'] ?? '',
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
            'slug' => $this->slug,
        ];
    }
}
