<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class RawFindingData
{
    public function __construct(
        public string $title,
        public ?int $id = null,
        public ?string $description = null,
        public string $severity = 'medium',
        public string $status = 'new',
        public ?int $scanId = null,
        /** @var array<string, mixed>|null */
        public ?array $details = null,
        /** @var array<int, mixed> */
        public array $attachments = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            title: $data['title'],
            id: $data['id'] ?? null,
            description: $data['description'] ?? null,
            severity: $data['severity'] ?? 'medium',
            status: $data['status'] ?? 'new',
            scanId: $data['scan_id'] ?? null,
            details: $data['details'] ?? null,
            attachments: $data['attachments'] ?? [],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'id' => $this->id,
            'description' => $this->description,
            'severity' => $this->severity,
            'status' => $this->status,
            'scan_id' => $this->scanId,
            'details' => $this->details,
            'attachments' => $this->attachments,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
