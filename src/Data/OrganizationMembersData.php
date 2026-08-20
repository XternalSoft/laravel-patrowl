<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class OrganizationMembersData
{
    public function __construct(
        public string $owner,
        public int $userCount
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            owner: (string) ($data['owner'] ?? ''),
            userCount: (int) ($data['user_count'] ?? 0)
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'owner' => $this->owner,
            'user_count' => $this->userCount,
        ];
    }
}
