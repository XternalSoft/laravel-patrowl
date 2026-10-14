<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class BulkAssociateTagsData
{
    /**
     * @param  array<int, int>  $assetIds
     * @param  array<int, int>  $tagIds
     */
    public function __construct(
        public array $assetIds,
        public array $tagIds,
        public ?int $organizationId = null
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'asset_ids' => $this->assetIds,
            'tag_ids' => $this->tagIds,
            'organization_id' => $this->organizationId,
        ];

        return array_filter($data, fn ($value) => $value !== null);
    }
}
