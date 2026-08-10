<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to retrieve technology aggregates using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/technology_aggregates.php
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

echo "--- RETRIEVING TECHNOLOGY AGGREGATES (POST /asm/technology/aggregates/) ---\n\n";

try {
    // Retrieve aggregates for some technology IDs (e.g. 1, 2, 3)
    $ids = [1, 2, 3];
    $aggregates = $connector->technologies()->aggregates($ids);

    foreach ($aggregates as $aggregate) {
        /** @var Xternalsoft\LaravelPatrowl\Data\TechnologyAggregateData $aggregate */
        echo sprintf(
            "Technology ID: %d\n".
            "  - Assets Count: %d\n".
            "  - CVEs Count: %d\n".
            "  - Last Seen At: %s\n\n",
            $aggregate->id,
            $aggregate->assets,
            $aggregate->cves,
            $aggregate->lastSeenAt ?? 'N/A'
        );
    }

} catch (Throwable $e) {
    echo sprintf("ERROR: %s\n", $e->getMessage());
    exit(1);
}
