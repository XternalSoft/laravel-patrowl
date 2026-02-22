<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use function config;

final class CreateAssetTagData
{
    public function __construct(
        public string $value,
        public ?int $organization = null,
        public ?int $id = null
    ) {}

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'value' => $this->value,
            'organization' => $this->organization ?? config('patrowl.default_organization_id'),
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
