<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

use Xternalsoft\LaravelPatrowl\Enums\RiskSeverityEnum;
use Xternalsoft\LaravelPatrowl\Enums\RiskStatusEnum;

final class RiskInListData
{
    public function __construct(
        public ?int $id = null,
        public ?string $title = null,
        public ?RiskSeverityEnum $severity = null,
        public ?RiskStatusEnum $status = null,
        public ?string $type = null,
        public ?int $assetId = null,
        public ?string $assetValue = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?string $topic = null,
        public ?int $topicId = null,
        public ?string $topicSlug = null,
        public ?string $subtopic = null,
        public ?int $subtopicId = null,
        public ?string $subtopicSlug = null,
        public ?string $description = null,
        /** @var array<int, string>|null */
        public ?array $assetTags = null,
        public ?string $lastSeenAt = null
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        $status = $data['status'] ?? null;

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

        $assetId = $data['asset'] ?? null;
        $assetValue = $data['asset_value'] ?? null;

        // Handle case where 'asset' is an array/object
        if (is_array($assetId)) {
            $assetValue = $assetId['value'] ?? $assetValue;
            $assetId = $assetId['id'] ?? null;
        }

        $assetTags = $data['asset_tags'] ?? null;
        if (is_string($assetTags)) {
            $assetTags = array_values(array_filter(explode('|', $assetTags)));
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
            id: $data['id'] ?? null,
            title: $data['title'] ?? null,
            severity: isset($data['severity']) ? RiskSeverityEnum::from($data['severity']) : null,
            status: $status ? RiskStatusEnum::from($status) : null,
            type: $data['type'] ?? null,
            assetId: is_numeric($assetId) ? (int) $assetId : null,
            assetValue: $assetValue,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            topic: is_string($topic) ? $topic : null,
            topicId: $topicId ? (int) $topicId : null,
            topicSlug: is_string($topicSlug) ? $topicSlug : null,
            subtopic: is_string($subtopic) ? $subtopic : null,
            subtopicId: $subtopicId ? (int) $subtopicId : null,
            subtopicSlug: is_string($subtopicSlug) ? $subtopicSlug : null,
            description: $data['description'] ?? $data['raw_data'] ?? null,
            assetTags: $assetTags,
            lastSeenAt: $data['last_seen_at'] ?? null
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
            'severity' => $this->severity?->value,
            'status' => $this->status?->value,
            'type' => $this->type,
            'asset_id' => $this->assetId,
            'asset_value' => $this->assetValue,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'topic' => $this->topic,
            'topic_id' => $this->topicId,
            'topic_slug' => $this->topicSlug,
            'subtopic' => $this->subtopic,
            'subtopic_id' => $this->subtopicId,
            'subtopic_slug' => $this->subtopicSlug,
            'description' => $this->description,
            'asset_tags' => $this->assetTags,
            'last_seen_at' => $this->lastSeenAt,
        ];
    }
}
