<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class FindingData
{
    public function __construct(
        public string $title,
        public ?int $id = null,
        public ?string $description = null,
        public string $status = 'in_check',
        public string $severity = 'medium',
        public string $type = 'vuln',
        public array $tags = [],
        public ?array $rawFinding = null,
        public array $assignees = [],
        public ?string $vulnId = null,
        public ?string $vulnType = null,
        public array $vulnRefs = [],
        public array $attachments = [],
        public array $assets = [],
        public array $assetgroups = [],
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?int $todosCount = null,
        public ?int $commentsCount = null
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
            status: $data['status'] ?? 'in_check',
            severity: $data['severity'] ?? 'medium',
            type: $data['type'] ?? 'vuln',
            tags: $data['tags'] ?? [],
            rawFinding: $data['raw_finding'] ?? null,
            assignees: $data['assignees'] ?? [],
            vulnId: $data['vuln_id'] ?? null,
            vulnType: $data['vuln_type'] ?? null,
            vulnRefs: $data['vuln_refs'] ?? [],
            attachments: $data['attachments'] ?? [],
            assets: $data['assets'] ?? [],
            assetgroups: $data['assetgroups'] ?? [],
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            todosCount: $data['todos_count'] ?? null,
            commentsCount: $data['comments_count'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'id' => $this->id,
            'description' => $this->description,
            'status' => $this->status,
            'severity' => $this->severity,
            'type' => $this->type,
            'tags' => $this->tags,
            'raw_finding' => $this->rawFinding,
            'assignees' => $this->assignees,
            'vuln_id' => $this->vulnId,
            'vuln_type' => $this->vulnType,
            'vuln_refs' => $this->vulnRefs,
            'attachments' => $this->attachments,
            'assets' => $this->assets,
            'assetgroups' => $this->assetgroups,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'todos_count' => $this->todosCount,
            'comments_count' => $this->commentsCount,
        ];
    }
}
