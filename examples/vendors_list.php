<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list vendors using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/vendors_list.php
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

echo "--- LISTING VENDORS (GET /asm/vendor/) ---\n\n";

try {
    // List all vendors with auto-pagination
    $paginator = $connector->technologies()->vendors();

    $count = 0;
    foreach ($paginator->items() as $vendor) {
        /** @var Xternalsoft\LaravelPatrowl\Data\VendorData $vendor */
        echo sprintf(
            "[%d] %s\n",
            $vendor->id,
            $vendor->name
        );

        $count++;
    }

    echo sprintf("\nTotal vendors found: %d\n", $count);

} catch (Throwable $e) {
    echo sprintf("ERROR: %s\n", $e->getMessage());
    exit(1);
}
