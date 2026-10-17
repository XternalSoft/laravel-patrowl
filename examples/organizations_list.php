<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/organizations_list.php\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl
);

try {
    echo "Fetching organizations...\n";
    $organizations = $client->organizations()->all();

    foreach ($organizations as $org) {
        echo "------------------------------------------------------------\n";
        echo "Organization Name : {$org->name}\n";
        echo "ID                : {$org->id}\n";
        echo "Slug              : {$org->slug}\n";
        echo "Tenant            : {$org->tenantName} (ID: {$org->tenantId})\n";
    }
    echo "------------------------------------------------------------\n";
    echo "Done.\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
