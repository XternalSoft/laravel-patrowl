<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkAssociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkDissociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetTagsRequest;

final readonly class AssetTagResource
{
    public function __construct(private LaravelPatrowl $connector) {}

    /**
     * Get all asset tags with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_list
     */
    public function all(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetAssetTagsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Bulk associate tags with assets.
     */
    public function associate(BulkAssociateTagsData $data): \Saloon\Http\Response
    {
        return $this->connector->send(new BulkAssociateTagsRequest($data, $this->connector->getDefaultOrganizationId()));
    }

    /**
     * Bulk dissociate tags from assets.
     *
     * @param  array<int, int>  $assetIds
     * @param  array<int, int>  $tagIds
     */
    public function dissociate(array $assetIds, array $tagIds): \Saloon\Http\Response
    {
        return $this->connector->send(new BulkDissociateTagsRequest($assetIds, $tagIds));
    }

    /**
     * List all tags with auto-pagination.
     *
     * @param  array<string, mixed>  $queryParams
     */
    public function tags(array $queryParams = []): Paginator
    {
        return $this->connector->paginate(new GetTagsRequest(
            $queryParams,
            $this->connector->getDefaultOrganizationId(),
            $this->connector->getLimit()
        ));
    }

    /**
     * Get a specific asset tag.
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_retrieve
     */
    public function get(int $id): AssetTagData
    {
        return $this->connector->send(new GetAssetTagRequest($id))->dtoOrFail();
    }
}
