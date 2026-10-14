<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Resources;

use Saloon\PaginationPlugin\Paginator;
use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkAssociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\BulkDissociateTagsRequest;
use Xternalsoft\LaravelPatrowl\Requests\AssetTags\GetTagsRequest;

final readonly class AssetTagResource
{
    public function __construct(private LaravelPatrowl $connector) {}

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
        return $this->connector->send(new BulkDissociateTagsRequest(
            $assetIds,
            $tagIds,
            $this->connector->getDefaultOrganizationId()
        ));
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
}
