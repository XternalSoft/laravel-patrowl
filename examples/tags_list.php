<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list tags using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/tags_list.php
 */

// Configuration
$token = getenv('PATROWL_API_TOKEN') ?: 'YOUR_API_TOKEN';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ?: null;
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if ($token === 'YOUR_API_TOKEN') {
    echo "Please provide your API token via PATROWL_API_TOKEN environment variable.\n";
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId ? (int) $orgId : null
);

echo "--- LISTING TAGS (GET /tags/) ---\n\n";

try {
    // List all tags with auto-pagination, requesting related fields
    $paginator = $connector->assetTags()->tags([
        'related' => true,
    ]);

    $count = 0;
    foreach ($paginator->items() as $tag) {
        /** @var Xternalsoft\LaravelPatrowl\Data\TagData $tag */
        echo sprintf(
            "[%d] %s (Description: %s, Organization: %d)\n",
            $tag->id,
            $tag->value,
            $tag->description ?? 'N/A',
            $tag->organization ?? 0
        );

        if (! empty($tag->assets)) {
            echo "   Linked Assets:\n";
            foreach ($tag->assets as $asset) {
                echo sprintf("     - [%d] %s\n", $asset['id'], $asset['value']);
            }
        }

        if (! empty($tag->assetGroups)) {
            echo "   Linked Asset Groups:\n";
            foreach ($tag->assetGroups as $group) {
                echo sprintf("     - [%d] %s\n", $group['id'], $group['title']);
            }
        }

        echo "\n";
        $count++;
    }

    echo sprintf("Total tags found: %d\n", $count);

} catch (Throwable $e) {
    echo sprintf("ERROR: %s\n", $e->getMessage());
    exit(1);
}
