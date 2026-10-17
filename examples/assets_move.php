<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

$usage = "Usage: PATROWL_API_TOKEN=your_token php examples/assets_move.php <asset_id> <target_organization_id> [--delete-source]\n";

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo $usage;
    exit(1);
}

$assetId = isset($argv[1]) ? (int) $argv[1] : null;
$targetOrganizationId = isset($argv[2]) ? (int) $argv[2] : null;
$deleteSource = in_array('--delete-source', $argv, true);

if (! $assetId || ! $targetOrganizationId) {
    echo "Error: Please specify an Asset ID and a target Organization ID.\n";
    echo $usage;
    exit(1);
}

$connector = new LaravelPatrowl(apiToken: $token, baseUrl: $baseUrl);

try {
    echo "Moving asset {$assetId} to organization {$targetOrganizationId}".($deleteSource ? ' (source will be deleted)' : ' (source kept)')."...\n";
    $result = $connector->assets()->move($assetId, $targetOrganizationId, $deleteSource);

    echo "========================================\n";
    echo "New asset:      {$result->asset->id} ({$result->asset->value})\n";
    echo 'Created tags:   '.($result->createdTags ? implode(', ', $result->createdTags) : 'none')."\n";
    echo 'Failed tags:    '.($result->failedTags ? implode(', ', $result->failedTags) : 'none')."\n";
    echo 'Source deleted: '.($result->sourceDeleted ? 'Yes' : 'No')."\n";
    echo 'Owners not copied: '.count($result->sourceOwners)."\n";
    echo 'Groups not copied: '.count($result->sourceGroups)."\n";
    echo "========================================\n";
} catch (AssetAlreadyExistsException $e) {
    echo "The asset already exists in the target organization, the source was left untouched.\n";
    exit(1);
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
