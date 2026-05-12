<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list assets using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_TOKEN=your_token PATROWL_ORG_ID=your_org_id php examples/list_assets.php
 */

// Configuration
$token = getenv('PATROWL_TOKEN') ?: 'YOUR_API_TOKEN';
$orgId = getenv('PATROWL_ORG_ID') ?: null;
$baseUrl = getenv('PATROWL_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if ($token === 'YOUR_API_TOKEN') {
    echo "Please provide your API token via PATROWL_TOKEN environment variable or edit the script.\n";
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId ? (int) $orgId : null
);

echo "--- LISTING ASSETS ---\n";

try {
    $paginator = $connector->assets()->all();

    $count = 0;
    foreach ($paginator->items() as $asset) {
        /** @var Xternalsoft\LaravelPatrowl\Data\AssetInListData $asset */
        echo sprintf(
            "[%d] %s (Type: %s, Score: %d, Criticality: %s)\n",
            $asset->id,
            $asset->value,
            $asset->type?->value ?? 'N/A',
            $asset->score,
            $asset->criticality?->name ?? 'N/A'
        );
        $count++;
    }

    echo "\nTotal assets found: $count\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
