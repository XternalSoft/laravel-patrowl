<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list products using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/products_list.php
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

echo "--- LISTING PRODUCTS (GET /asm/product/) ---\n\n";

try {
    // List all products with auto-pagination
    $paginator = $connector->technologies()->products();

    $count = 0;
    foreach ($paginator->items() as $product) {
        /** @var Xternalsoft\LaravelPatrowl\Data\ProductData $product */
        echo sprintf(
            "[%d] %s (Vendor: %s)\n",
            $product->id,
            $product->name,
            $product->vendor
        );

        $count++;
    }

    echo sprintf("\nTotal products found: %d\n", $count);

} catch (Throwable $e) {
    echo sprintf("ERROR: %s\n", $e->getMessage());
    exit(1);
}
