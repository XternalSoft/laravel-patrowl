<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class ScanData
{
    public function __construct(
        public ?int $scanDefinitionId = null,
        public ?int $engineId = null,
        public ?string $startedAt = null,
        public ?string $endedAt = null,
        public array $targetAssets = [],
        public array $targetAssetgroups = [],
        public ?int $id = null,
        public ?string $status = null,
        public ?string $scanDefinitionType = null,
        public ?string $engineType = null,
        public ?int $duration = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null,
        public ?int $todosCount = null,
        public ?int $commentsCount = null
    ) {
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApi(array $data): self
    {
        return new self(
            scanDefinitionId: $data['scan_definition_id'] ?? null,
            engineId: $data['engine_id'] ?? null,
            startedAt: $data['started_at'] ?? null,
            endedAt: $data['ended_at'] ?? null,
            targetAssets: $data['target_assets'] ?? [],
            targetAssetgroups: $data['target_assetgroups'] ?? [],
            id: $data['id'] ?? null,
            status: $data['status'] ?? null,
            scanDefinitionType: $data['scan_definition_type'] ?? null,
            engineType: $data['engine_type'] ?? null,
            duration: $data['duration'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            todosCount: $data['todos_count'] ?? null,
            commentsCount: $data['comments_count'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'scan_definition_id' => $this->scanDefinitionId,
            'engine_id' => $this->engineId,
            'started_at' => $this->startedAt,
            'ended_at' => $this->endedAt,
            'target_assets' => $this->targetAssets,
            'target_assetgroups' => $this->targetAssetgroups,
            'id' => $this->id,
            'status' => $this->status,
            'scan_definition_type' => $this->scanDefinitionType,
            'engine_type' => $this->engineType,
            'duration' => $this->duration,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'todos_count' => $this->todosCount,
            'comments_count' => $this->commentsCount,
        ];
    }
}
