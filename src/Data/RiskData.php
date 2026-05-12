<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\RiskSeverityEnum;
use Xternalsoft\LaravelPatrowl\Enums\RiskStatusEnum;

final class RiskData
{
    public function __construct(
        public string $title,
        public ?int $id = null,
        public ?string $description = null,
        public RiskStatusEnum $status = RiskStatusEnum::New,
        public RiskSeverityEnum $severity = RiskSeverityEnum::Medium,
        public ?string $type = null,
        /** @var array<int, mixed> */
        public array $tags = [],
        /** @var array<string, mixed>|null */
        public ?array $rawFinding = null,
        /** @var array<int, mixed> */
        public array $assignees = [],
        public ?int $vulnId = null,
        public ?string $vulnType = null,
        /** @var array<int, mixed> */
        public array $vulnRefs = [],
        /** @var array<int, mixed> */
        public array $attachments = [],
        /** @var array<int, mixed> */
        public array $assets = [],
        /** @var array<int, mixed> */
        public array $assetgroups = [],
        public ?string $topic = null,
        public ?int $topicId = null,
        public ?string $topicSlug = null,
        public ?string $subtopic = null,
        public ?int $subtopicId = null,
        public ?string $subtopicSlug = null,
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
        $status = $data['status'] ?? 'new';

        // Handle integer status from API
        if (is_int($status)) {
            $status = match ($status) {
                0 => 'new',
                1 => 'ack',
                2 => 'assigned',
                3 => 'patched',
                4 => 'closed',
                5 => 'closed-benign',
                6 => 'closed-fp',
                7 => 'closed-duplicate',
                8 => 'closed-workaround',
                9 => 'closed-risk-acceptance',
                default => 'new',
            };
        }

        $topicData = $data['topic'] ?? null;
        $topic = null;
        $topicId = null;
        $topicSlug = null;
        if (is_array($topicData)) {
            $topic = $topicData['title'] ?? $topicData['name'] ?? $topicData['value'] ?? null;
            $topicId = $topicData['id'] ?? null;
            $topicSlug = $topicData['slug'] ?? null;
        } elseif (is_string($topicData)) {
            $topic = $topicData;
        }

        $subtopicData = $data['subtopic'] ?? null;
        $subtopic = null;
        $subtopicId = null;
        $subtopicSlug = null;
        if (is_array($subtopicData)) {
            $subtopic = $subtopicData['title'] ?? $subtopicData['name'] ?? $subtopicData['value'] ?? null;
            $subtopicId = $subtopicData['id'] ?? null;
            $subtopicSlug = $subtopicData['slug'] ?? null;
        } elseif (is_string($subtopicData)) {
            $subtopic = $subtopicData;
        }

        return new self(
            title: $data['title'],
            id: $data['id'] ?? null,
            description: $data['description'] ?? null,
            status: RiskStatusEnum::from($status),
            severity: RiskSeverityEnum::from($data['severity'] ?? 2),
            type: $data['type'] ?? null,
            tags: $data['tags'] ?? [],
            rawFinding: $data['raw_finding'] ?? null,
            assignees: $data['assignees'] ?? [],
            vulnId: $data['vuln_id'] ?? null,
            vulnType: $data['vuln_type'] ?? null,
            vulnRefs: $data['vuln_refs'] ?? [],
            attachments: $data['attachments'] ?? [],
            assets: $data['assets'] ?? [],
            assetgroups: $data['assetgroups'] ?? [],
            topic: is_string($topic) ? $topic : null,
            topicId: $topicId ? (int) $topicId : null,
            topicSlug: is_string($topicSlug) ? $topicSlug : null,
            subtopic: is_string($subtopic) ? $subtopic : null,
            subtopicId: $subtopicId ? (int) $subtopicId : null,
            subtopicSlug: is_string($subtopicSlug) ? $subtopicSlug : null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            todosCount: $data['todos_count'] ?? null,
            commentsCount: $data['comments_count'] ?? null
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
            'status' => $this->status->value,
            'severity' => $this->severity->value,
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
            'topic' => $this->topic,
            'topic_id' => $this->topicId,
            'topic_slug' => $this->topicSlug,
            'subtopic' => $this->subtopic,
            'subtopic_id' => $this->subtopicId,
            'subtopic_slug' => $this->subtopicSlug,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'todos_count' => $this->todosCount,
            'comments_count' => $this->commentsCount,
        ];
    }
}
