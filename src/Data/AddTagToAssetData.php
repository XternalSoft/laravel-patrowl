<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use function config;

final class AddTagToAssetData
{
    public function __construct(
        public string $value,
        public ?int $organization = null
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'value' => $this->value,
            'organization' => $this->organization,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
