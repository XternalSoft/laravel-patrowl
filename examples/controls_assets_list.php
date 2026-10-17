<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/controls_assets_list.php [control_id]\n";
    exit(1);
}

$controlId = isset($argv[1]) ? (int) $argv[1] : null;

if (! $controlId) {
    echo "Error: Please specify a Control ID.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/controls_assets_list.php <control_id>\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    echo "Fetching assets of control ID: {$controlId}...\n";

    $count = 0;
    foreach ($client->controls()->assets($controlId)->items() as $asset) {
        $count++;
        echo "{$asset->id} | {$asset->value} | {$asset->type} | {$asset->controlImpact}\n";
    }

    echo "Found {$count} assets.\nDone.\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
