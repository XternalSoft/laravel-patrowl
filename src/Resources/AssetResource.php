<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetInListData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Assets\AddTagToAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\BulkUpdateAssetsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\CreateAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\DeleteAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\RefreshAssetScoreRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\RemoveAssetTagFromAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\ReplaceAssetRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\SyncAssetTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\Assets\UpdateAssetRequest;

final readonly class AssetResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all assets with auto-pagination.
     *
     * @param array<string, mixed> $queryParams
     * @return Paginator
     *
     * @see https://developer.patrowl.io/#operation/get-assets
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetAssetsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Create a new asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_create
     */
    public function create(CreateAssetData $data): AssetData
    {
        return $this->connector->send(new CreateAssetRequest($data, $this->connector->getDefaultOrganizationId()))->dtoOrFail();
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
     * @param array<string, mixed> $data
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
     * @param array<string, mixed> $data
     * @see https://developer.patrowl.io/#operation/assets_bulk_partial_update
     */
    public function bulkUpdate(array $data): Response
    {
        return $this->connector->send(new BulkUpdateAssetsRequest($data));
    }

    /**
     * Replace an asset.
     *
     * @param array<string, mixed> $data
     * @see https://developer.patrowl.io/#operation/assets_update
     */
    public function replace(int $id, array $data): AssetData
    {
        return $this->connector->send(new ReplaceAssetRequest($id, $data))->dtoOrFail();
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
     * @param array<int, int> $tagIds
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
