<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/assets_get.php <asset_id>\n";
    exit(1);
}

$assetId = isset($argv[1]) ? (int) $argv[1] : null;

if (! $assetId) {
    echo "Error: Please specify an Asset ID.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/assets_get.php <asset_id>\n";
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    echo "Fetching detailed asset ID: {$assetId}...\n";
    $asset = $connector->assets()->get($assetId);

    echo "========================================\n";
    echo "ASSET DETAILS\n";
    echo "========================================\n";
    echo "ID:          {$asset->id}\n";
    echo "Value:       {$asset->value}\n";
    echo "Type:        {$asset->type->value}\n";
    echo "Criticality: {$asset->criticality->value}\n";
    echo "Exposure:    {$asset->exposure->value}\n";
    echo "Score:       {$asset->score} (Level: {$asset->score_level})\n";
    echo 'Active:      '.($asset->is_active ? 'Yes' : 'No')."\n";
    echo "Liveness:    {$asset->liveness->value}\n";
    echo "Created By:  {$asset->created_by}\n";
    echo "Created At:  {$asset->created_at}\n";
    echo "Updated At:  {$asset->updated_at}\n";
    echo "Description: {$asset->description}\n";

    if ($asset->www_related_domain) {
        echo "Related Domain: {$asset->www_related_domain->value}\n";
    }

    echo "\n----------------------------------------\n";
    $tagsCount = $asset->asset_tags ? count($asset->asset_tags) : 0;
    echo "Asset Tags ({$tagsCount}):\n";
    if ($asset->asset_tags) {
        foreach ($asset->asset_tags as $tag) {
            echo "  - ID: {$tag->id} | Value: {$tag->value} | Organization: {$tag->organization}\n";
        }
    }

    echo "\n----------------------------------------\n";
    $groupsCount = $asset->groups ? count($asset->groups) : 0;
    echo "Asset Groups ({$groupsCount}):\n";
    if ($asset->groups) {
        foreach ($asset->groups as $group) {
            echo "  - ID: {$group->id} | Title: ".($group->title ?: 'N/A')."\n";
        }
    }

    echo "\n----------------------------------------\n";
    $techsCount = $asset->technologies ? count($asset->technologies) : 0;
    echo "Technologies ({$techsCount}):\n";
    if ($asset->technologies) {
        foreach ($asset->technologies as $tech) {
            $name = ($tech->vendor ? $tech->vendor.' - ' : '').($tech->product ?: 'Unknown');
            echo "  - {$name} (Version: ".($tech->version ?: 'N/A').")\n";
        }
    }

    echo "========================================\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
