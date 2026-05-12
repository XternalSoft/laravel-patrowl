<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to export risks to CSV using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_TOKEN=your_token PATROWL_ORG_ID=your_org_id php examples/export_risks_csv.php
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

echo "--- EXPORTING RISKS TO CSV ---\n";

try {
    $csvContent = $connector->risks()->exportCsv();

    if (empty($csvContent)) {
        echo "No content received from export.\n";
        exit;
    }

    $filename = 'risks_export_'.date('Ymd_His').'.csv';
    file_put_contents($filename, $csvContent);

    echo "Successfully exported to $filename\n";
    echo "First 100 characters of CSV:\n";
    echo mb_substr($csvContent, 0, 100)."...\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
