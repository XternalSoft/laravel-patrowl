<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Data\MoveAssetResultData;
use Xternalsoft\LaravelPatrowl\Data\TagData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Assets\AddTagToAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\BulkUpdateAssetsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\CreateAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\DeleteAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\RefreshAssetScoreRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\RemoveAssetTagFromAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\SyncAssetTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\UpdateAssetRequest;

final readonly class AssetResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all assets with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#operation/get-assets
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetAssetsRequest(
            $queryParams,
            $this->connector->getLimit()
        ));
    }

    /**
     * Create a new asset.
     *
     * @throws \Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException
     *
     * @see https://developer.patrowl.io/#operation/assets_create
     */
    public function create(CreateAssetData $data): AssetData
    {
        return $this->connector->send(new CreateAssetRequest($data, $this->connector->getDefaultOrganizationId()))->dtoOrFail();
    }

    /**
     * Move an asset to another organization: the asset is created in the target
     * organization first, and the source is deleted only if that creation succeeded.
     *
     * Tags are matched by value in the target organization and created when missing.
     * Owners, teams and groups are not transferred (the source ones are returned in the result), and the history (findings, score,
     * controls) of the source asset is lost with its deletion.
     *
     * @throws \Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException when the asset already exists in the target organization (the source is left untouched)
     */
    public function move(int $assetId, int $targetOrganizationId, bool $deleteSource = true): MoveAssetResultData
    {
        $source = $this->get($assetId);

        $targetTagIds = $this->connector->assetTags()->tags(['org_id' => $targetOrganizationId])
            ->collect()
            ->mapWithKeys(fn (TagData $tag): array => [$tag->value => $tag->id])
            ->all();

        $sourceTagValues = array_map(fn (AssetTagData $tag): string => $tag->value, $source->asset_tags ?? []);
        $existingTagIds = array_values(array_intersect_key($targetTagIds, array_flip($sourceTagValues)));
        $missingTagValues = array_values(array_diff($sourceTagValues, array_keys($targetTagIds)));

        $asset = $this->create(new CreateAssetData(
            value: $source->value,
            organization: $targetOrganizationId,
            description: $source->description,
            criticality: $source->criticality,
            exposure: $source->exposure,
            tags: $existingTagIds ?: null,
        ));

        $createdTags = [];
        $failedTags = [];
        foreach ($missingTagValues as $value) {
            $response = $this->addTag($asset->id, new AddTagToAssetData($value, $targetOrganizationId));

            if ($response->successful()) {
                $createdTags[] = $value;
            } else {
                $failedTags[] = $value;
            }
        }

        $sourceDeleted = $deleteSource && $this->delete($assetId)->successful();

        return new MoveAssetResultData($asset, $createdTags, $failedTags, $sourceDeleted, $source->asset_owners ?? [], $source->groups ?? []);
    }

    /**
     * Get a specific asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_retrieve
     */
    public function get(int $id): AssetData
    {
        return $this->connector->send(new GetAssetRequest($id))->dtoOrFail();
    }

    /**
     * Update an asset.
     *
     * @param  array<string, mixed>  $data
     *
     * @see https://developer.patrowl.io/#operation/assets_partial_update
     */
    public function update(int $id, array $data): AssetData
    {
        return $this->connector->send(new UpdateAssetRequest($id, $data))->dtoOrFail();
    }

    /**
     * Delete an asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_delete
     */
    public function delete(int $id): Response
    {
        return $this->connector->send(new DeleteAssetRequest($id));
    }

    /**
     * Update several assets.
     *
     * @param  array<string, mixed>  $data
     *
     * @see https://developer.patrowl.io/#operation/assets_bulk_partial_update
     */
    public function bulkUpdate(array $data): Response
    {
        return $this->connector->send(new BulkUpdateAssetsRequest($data));
    }

    /**
     * Refresh an asset score.
     *
     * @see https://developer.patrowl.io/#operation/assets_refresh_score
     */
    public function refreshScore(int $id): Response
    {
        return $this->connector->send(new RefreshAssetScoreRequest($id));
    }

    /**
     * Sets or removes tags from an asset by providing a list of tag IDs.
     * Providing an empty array will remove all tags.
     *
     * @param  array<int, int>  $tagIds
     *
     * @see https://developer.patrowl.io/#asset_remove_tags
     */
    public function syncTags(int $assetId, array $tagIds): Response
    {
        return $this->connector->send(new SyncAssetTagsRequest($assetId, $tagIds));
    }

    /**
     * Remove a specific AssetTag from an asset.
     *
     * @see https://developer.patrowl.io/?shell#assets_remove_tag
     */
    public function removeTag(int $assetId, int $tagId): Response
    {
        return $this->connector->send(new RemoveAssetTagFromAssetRequest($assetId, $tagId));
    }

    /**
     * Add a tag to an asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_add_tag
     */
    public function addTag(int $assetId, AddTagToAssetData $data): Response
    {
        return $this->connector->send(new AddTagToAssetRequest($assetId, $data, $this->connector->getDefaultOrganizationId()));
    }
}
