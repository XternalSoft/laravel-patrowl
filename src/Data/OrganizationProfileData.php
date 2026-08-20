<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationProfileData
{
    public function __construct(
        public bool $isPoc,
        public string $createdAt,
        public ?string $endOfContract = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            isPoc: (bool) ($data['is_poc'] ?? false),
            createdAt: (string) ($data['created_at'] ?? ''),
            endOfContract: $data['end_of_contract'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'is_poc' => $this->isPoc,
            'created_at' => $this->createdAt,
            'end_of_contract' => $this->endOfContract,
        ];
    }
}
