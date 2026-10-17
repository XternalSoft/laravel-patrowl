<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Data;

final class MoveAssetResultData
{
    public function __construct(
        public AssetData $asset,
        /** @var array<int, string> */
        public array $createdTags,
        /** @var array<int, string> */
        public array $failedTags,
        public bool $sourceDeleted,
        /** @var array<AssetOwnerData> */
        public array $sourceOwners = [],
        /** @var array<AssetGroupLiteData> */
        public array $sourceGroups = []
    ) {}
}
