<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl;

use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\AssetGroupInListData;
use Xternalsoft\LaravelPatrowl\Data\AssetInListData;
use Xternalsoft\LaravelPatrowl\Data\AssetTagData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\Data\CreateAssetTagData;
use Xternalsoft\LaravelPatrowl\Data\PaginatedResponseData;
use Xternalsoft\LaravelPatrowl\Exceptions\MissingApiTokenException;

final class LaravelPatrowl
{
    private PendingRequest $client;

    private ?string $apiToken;

    private string $baseUrl = '';

    private ?int $defaultOrganizationId;

    private int $limit;

    public function __construct()
    {
        $this->apiToken = config('patrowl.api_token');
        $this->baseUrl = config('patrowl.base_url');
        $this->defaultOrganizationId = config('patrowl.default_organization_id') ? (int) config('patrowl.default_organization_id') : null;
        $this->limit = (int) config('patrowl.limit', 100);

        $this->client = Http::withHeaders([
            'Authorization' => 'Token '.$this->apiToken,
        ])
            ->baseUrl($this->baseUrl)
            ->timeout(config('patrowl.timeout', 30))
            ->throw()
            ->acceptJson();
    }

    public function getDefaultOrganizationId(): ?int
    {
        return $this->defaultOrganizationId;
    }

    /**
     * Get all assets with auto-pagination.
     *
     * @return Generator<AssetInListData>
     *
     * @throws MissingApiTokenException
     *
     * @see https://developer.patrowl.io/#operation/get-assets
     */
    public function getAssets(array $queryParams = []): Generator
    {
        return $this->paginate('/assets', AssetInListData::class, $queryParams);
    }

    /**
     * Create a new asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_create
     *
     * @throws MissingApiTokenException
     */
    public function createAsset(CreateAssetData $data): AssetData
    {
        $asset = $this->makeRequest('post', '/assets', $data->toArray())->json();

        return AssetData::fromApi($asset);
    }

    /**
     * Get a specific asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_retrieve
     *
     * @throws MissingApiTokenException
     */
    public function getAsset(int $id): AssetData
    {
        $asset = $this->makeRequest('get', "/assets/{$id}")->json();

        return AssetData::fromApi($asset);
    }

    /**
     * Update an asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_partial_update
     *
     * @throws MissingApiTokenException
     */
    public function updateAsset(int $id, array $data): AssetData
    {
        $asset = $this->makeRequest('patch', "/assets/{$id}", $data)->json();

        return AssetData::fromApi($asset);
    }

    /**
     * Delete an asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_delete
     *
     * @throws MissingApiTokenException
     */
    public function deleteAsset(int $id): Response
    {
        return $this->makeRequest('delete', "/assets/{$id}");
    }

    /**
     * Update several assets.
     *
     * @see https://developer.patrowl.io/#operation/assets_bulk_partial_update
     *
     * @throws MissingApiTokenException
     */
    public function bulkUpdateAssets(array $data): Response
    {
        return $this->makeRequest('patch', '/assets', $data);
    }

    /**
     * Replace an asset.
     *
     * @see https://developer.patrowl.io/#operation/assets_update
     *
     * @throws MissingApiTokenException
     */
    public function replaceAsset(int $id, array $data): AssetData
    {
        $asset = $this->makeRequest('post', "/assets/{$id}", $data)->json();

        return AssetData::fromApi($asset);
    }

    /**
     * Refresh an asset score.
     *
     * @see https://developer.patrowl.io/#operation/assets_refresh_score
     *
     * @throws MissingApiTokenException
     */
    public function refreshAssetScore(int $id): Response
    {
        return $this->makeRequest('get', "/assets/{$id}/refresh");
    }

    /**
     * Get all asset groups with auto-pagination.
     *
     * @return Generator<AssetGroupInListData>
     *
     * @throws MissingApiTokenException
     *
     * @see https://developer.patrowl.io/#operation/assets_group_list
     */
    public function getAssetGroups(array $queryParams = []): Generator
    {
        return $this->paginate('/assets/group/', AssetGroupInListData::class, $queryParams);
    }

    /**
     * Create a new asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_create
     *
     * @throws MissingApiTokenException
     */
    public function createAssetGroup(CreateAssetGroupData $data): AssetGroupData
    {
        $assetGroup = $this->makeRequest('post', '/assets/group/', $data->toArray())->json();

        return AssetGroupData::fromApi($assetGroup);
    }

    /**
     * Get a specific asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_retrieve
     *
     * @throws MissingApiTokenException
     */
    public function getAssetGroup(int $id): AssetGroupData
    {
        $assetGroup = $this->makeRequest('get', "/asset-groups/{$id}")->json();

        return AssetGroupData::fromApi($assetGroup);
    }

    /**
     * Update an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_partial_update
     *
     * @throws MissingApiTokenException
     */
    public function updateAssetGroup(int $id, array $data): AssetGroupData
    {
        $assetGroup = $this->makeRequest('patch', "/asset-groups/{$id}", $data)->json();

        return AssetGroupData::fromApi($assetGroup);
    }

    /**
     * Delete an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_destroy
     *
     * @throws MissingApiTokenException
     */
    public function deleteAssetGroup(int $id): Response
    {
        return $this->makeRequest('delete', "/asset-groups/{$id}");
    }

    /**
     * Add assets to an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_add_assets
     *
     * @throws MissingApiTokenException
     */
    public function addAssetsToGroup(int $groupId, array $assetIds): Response
    {
        return $this->makeRequest('post', "/asset-groups/{$groupId}/add-assets", ['asset_ids' => $assetIds]);
    }

    /**
     * Remove assets from an asset group.
     *
     * @see https://developer.patrowl.io/#operation/assets_group_remove_assets
     *
     * @throws MissingApiTokenException
     */
    public function removeAssetsFromGroup(int $groupId, array $assetIds): Response
    {
        return $this->makeRequest('post', "/asset-groups/{$groupId}/remove-assets", ['asset_ids' => $assetIds]);
    }

    /**
     * Create a new asset tag.
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_create
     *
     * @throws MissingApiTokenException
     */
    public function createAssetTag(CreateAssetTagData $data): AssetTagData
    {
        $assetTag = $this->makeRequest('post', '/assets/tags/', $data->toArray())->json();

        return AssetTagData::fromApi($assetTag);
    }

    /**
     * Get a specific asset tag.
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_retrieve
     *
     * @throws MissingApiTokenException
     */
    public function getAssetTag(int $id): AssetTagData
    {
        $assetTag = $this->makeRequest('get', "/assets/tags/{$id}/")->json();

        return AssetTagData::fromApi($assetTag);
    }

    /**
     * Sets or removes tags from an asset by providing a list of tag IDs.
     * Providing an empty array will remove all tags.
     *
     * @see https://developer.patrowl.io/#asset_remove_tags
     *
     * @throws MissingApiTokenException
     */
    public function syncAssetTags(int $assetId, array $tagIds): Response
    {
        return $this->makeRequest('post', "/assets/{$assetId}/tags", ['tags' => $tagIds]);
    }

    /**
     * Remove a specific AssetTag from an asset.
     *
     * @see https://developer.patrowl.io/?shell#assets_remove_tag
     *
     * @throws MissingApiTokenException
     */
    public function removeAssetTag(int $assetId, int $tagId): Response
    {
        return $this->makeRequest('delete', "/assets/{$assetId}/tags/{$tagId}/");
    }

    /**
     * Get all asset tags with auto-pagination.
     *
     * @return Generator<AssetTagData>
     *
     * @throws MissingApiTokenException
     *
     * @see https://developer.patrowl.io/#operation/assets_tags_list
     */
    public function getAssetTags(array $queryParams = []): Generator
    {
        return $this->paginate('/assets/tags/', AssetTagData::class, $queryParams);
    }

    /**
     * Add a tag to an asset.
     *
     *
     * @throws MissingApiTokenException
     */
    public function addTagToAsset(int $assetId, AddTagToAssetData $data): Response
    {
        return $this->makeRequest('post', "/assets/{$assetId}/tags/add", $data->toArray());
    }

    /**
     * @throws MissingApiTokenException
     */
    private function makeRequest(string $method, string $uri, array $data = []): Response
    {
        if (! $this->apiToken) {
            throw new MissingApiTokenException('Patrowl API token is not configured.');
        }

        // If the URI is an absolute URL, we need to make the request without the base URL.
        if (str_starts_with($uri, 'http')) {
            return Http::withHeaders([
                'Authorization' => 'Token '.$this->apiToken,
            ])
                ->timeout(config('patrowl.timeout', 15))
                ->throw()
                ->acceptJson()
                ->{$method}($uri, $data);
        }

        return $this->client->{$method}($uri, $data);
    }

    private function addDefaultOrganizationId(array $queryParams): array
    {
        if (! isset($queryParams['org_id']) && ! isset($queryParams['organization']) && $this->defaultOrganizationId) {
            $queryParams['org_id'] = $this->defaultOrganizationId;
        }

        return $queryParams;
    }

    private function getNextPageNumber(?string $nextUrl): ?int
    {
        if ($nextUrl === null) {
            return null;
        }

        parse_str(parse_url($nextUrl, PHP_URL_QUERY), $query);

        return isset($query['page']) ? (int) $query['page'] : null;
    }

    private function paginate(string $endpoint, string $dtoClass, array $queryParams = []): Generator
    {
        $queryParams = $this->addDefaultOrganizationId($queryParams);
        $queryParams['limit'] = $queryParams['limit'] ?? $this->limit;
        $page = 1;

        while ($page !== null) {
            $queryParams['page'] = $page;
            /** @var PaginatedResponseData $response */
            $response = PaginatedResponseData::fromApi($this->makeRequest('get', $endpoint, $queryParams)->json(), $dtoClass);
            foreach ($response->results as $result) {
                yield $result;
            }

            $page = $this->getNextPageNumber($response->next);
        }
    }
}
