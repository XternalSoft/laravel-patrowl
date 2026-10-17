<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

$resources = ['domains', 'ipv4s', 'resolutions', 'asns', 'services', 'webServers', 'certificates', 'vendors', 'products', 'technologies', 'technologyDetections', 'cves'];
$resource = $argv[1] ?? 'domains';
$max = isset($argv[2]) ? (int) $argv[2] : 20;

if (! $token || ! in_array($resource, $resources, true)) {
    echo "Usage: PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=1 php examples/easm_list.php [resource] [max]\n";
    echo 'Resources: '.implode(', ', $resources)."\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    echo "Fetching {$resource} (max {$max})...\n";

    $count = 0;
    foreach ($client->{$resource}()->all()->items() as $item) {
        echo '------------------------------------------------------------'."\n";
        foreach ($item->toArray() as $key => $value) {
            echo mb_str_pad($key, 26).': '.(is_scalar($value) || $value === null ? var_export($value, true) : json_encode($value))."\n";
        }

        if (++$count >= $max) {
            break;
        }
    }

    echo '------------------------------------------------------------'."\n";
    echo "Displayed: {$count}\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
