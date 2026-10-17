<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class AssetScreenshotData
{
    public function __construct(
        public ?string $url = null,
        public ?string $takenAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            url: $data['url'] ?? null,
            takenAt: $data['taken_at'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'taken_at' => $this->takenAt,
        ];
    }
}
