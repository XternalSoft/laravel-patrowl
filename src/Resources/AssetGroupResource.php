<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\Http\Response;
use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\AddAssetsToGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\AddTagToAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\CreateAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\DeleteAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\GetAssetGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\GetAssetGroupsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\RemoveAssetsFromGroupRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetGroups\UpdateAssetGroupRequest;

final readonly class AssetGroupResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all asset groups with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#operation/assets_group_list
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetAssetGroupsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Create a new asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_create
     */
    public function create(CreateAssetGroupData $data): AssetGroupData
    {
        $group = $this->connector->send(new CreateAssetGroupRequest($data, $this->connector->getDefaultOrganizationId()))->dtoOrFail();

        if (! empty($data->assets)) {
            $this->addAssets($group->id, $data->assets);

            return $this->get($group->id);
        }

        return $group;
    }

    /**
     * Get a specific asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_retrieve
     */
    public function get(int $id): AssetGroupData
    {
        return $this->connector->send(new GetAssetGroupRequest($id))->dtoOrFail();
    }

    /**
     * Update an asset group.
     *
     * @param  array<string, mixed>  $data
     *
     * @see https://developer.patrowl.io/#operation/assets_group_partial_update
     */
    public function update(int $id, array $data): AssetGroupData
    {
        return $this->connector->send(new UpdateAssetGroupRequest($id, $data))->dtoOrFail();
    }

    /**
     * Delete an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_destroy
     */
    public function delete(int $id): Response
    {
        return $this->connector->send(new DeleteAssetGroupRequest($id));
    }

    /**
     * Add assets to an asset group.
     *
     * @param  array<int, int>  $assetIds
     *
     * @see https://developer.patrowl.io/#operation/assets_group_add_assets
     */
    public function addAssets(int $groupId, array $assetIds): Response
    {
        return $this->connector->send(new AddAssetsToGroupRequest($groupId, $assetIds));
    }

    /**
     * Remove assets from an asset group.
     *
     * @param  array<int, int>  $assetIds
     *
     * @see https://developer.patrowl.io/#operation/assets_group_remove_assets
     */
    public function removeAssets(int $groupId, array $assetIds): Response
    {
        return $this->connector->send(new RemoveAssetsFromGroupRequest($groupId, $assetIds));
    }

    /**
     * Add a tag to an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_add_assets_2
     */
    public function addTag(int $groupId, AddTagToAssetData $data): Response
    {
        return $this->connector->send(new AddTagToAssetGroupRequest($groupId, $data, $this->connector->getDefaultOrganizationId()));
    }
}
