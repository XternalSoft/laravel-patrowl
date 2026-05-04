<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetTagData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\CreateAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetAssetTagsRequest;

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
     * Create a new asset tag.
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_create
     */
    public function create(CreateAssetTagData $data): AssetTagData
    {
        return $this->connector->send(new CreateAssetTagRequest($data, $this->connector->getDefaultOrganizationId()))->dtoOrFail();
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
