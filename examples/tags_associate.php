<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to associate tags with assets in bulk.
 *
 * Usage:
 * PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/associate_tag.php <asset_id> <tag_id>
 */

// Configuration
$token = getenv('PATROWL_API_TOKEN') ?: 'YOUR_API_TOKEN';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ?: null;
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if ($token === 'YOUR_API_TOKEN') {
    echo "Please provide your API token via PATROWL_API_TOKEN environment variable.\n";
    exit(1);
}

if (! $orgId) {
    echo "Please provide your organization ID via PATROWL_DEFAULT_ORGANIZATION_ID environment variable.\n";
    exit(1);
}

$assetId = isset($argv[1]) ? (int) $argv[1] : null;
$tagId = isset($argv[2]) ? (int) $argv[2] : null;

if (! $assetId || ! $tagId) {
    echo "Usage: PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/tags_associate.php <asset_id> <tag_id>\n";
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: (int) $orgId
);

try {
    echo "--- PATROWL TAG ASSOCIATION EXAMPLE ---\n\n";

    echo "Bulk associating Tag [ID: {$tagId}] with Asset [ID: {$assetId}]...\n";

    $bulkData = new BulkAssociateTagsData(
        assetIds: [$assetId],
        tagIds: [$tagId],
        organizationId: (int) $orgId
    );

    $response = $connector->assetTags()->associate($bulkData);

    if ($response->successful()) {
        echo sprintf("   -> SUCCESS! %s (HTTP Status: 200)\n\n", $response->json('message'));
    } else {
        echo sprintf("   -> FAILED! HTTP Status: %d, Response: %s\n\n", $response->status(), $response->body());
    }

} catch (Throwable $e) {
    echo sprintf("ERROR: %s\n", $e->getMessage());
    exit(1);
}
