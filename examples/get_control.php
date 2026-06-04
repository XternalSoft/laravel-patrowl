<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/get_control.php [control_id]\n";
    exit(1);
}

$controlId = isset($argv[1]) ? (int) $argv[1] : null;

if (! $controlId) {
    echo "Error: Please specify a Control ID.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/get_control.php <control_id>\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    echo "Fetching detailed control ID: {$controlId}...\n";
    $control = $client->controls()->get($controlId);

    $statusValue = $control->status instanceof UnitEnum ? $control->status->name : $control->status;
    $resultValue = $control->result instanceof UnitEnum ? $control->result->name : $control->result;

    echo "========================================\n";
    echo "CONTROL DETAILS\n";
    echo "========================================\n";
    echo "ID:          {$control->id}\n";
    echo "Title:       {$control->title}\n";
    echo "Status:      {$statusValue}\n";
    echo "Result:      {$resultValue}\n";
    echo "Severity:    {$control->severity}\n";
    echo "CVSS Vector: {$control->cvssVector}\n";
    echo "CVSS Score:  {$control->cvssScore}\n";
    echo "Vendor:      {$control->vendor}\n";
    echo "Product:     {$control->product}\n";
    echo "Description: {$control->description}\n";

    echo "\n----------------------------------------\n";
    echo 'CVE Identifiers ('.count($control->cveIdentifiers)."):\n";
    foreach ($control->cveIdentifiers as $cve) {
        echo "  - {$cve}\n";
    }

    echo "\n----------------------------------------\n";
    echo 'References ('.count($control->references)."):\n";
    foreach ($control->references as $ref) {
        echo "  - {$ref}\n";
    }

    echo "\n----------------------------------------\n";
    echo 'Impacted Assets ('.count($control->assetsImpacted)."):\n";
    foreach ($control->assetsImpacted as $asset) {
        echo "  - ID: {$asset->id} | Value: {$asset->value}\n";
    }

    echo "\n----------------------------------------\n";
    echo 'Warning Assets ('.count($control->assetsWarning)."):\n";
    foreach ($control->assetsWarning as $asset) {
        echo "  - ID: {$asset->id} | Value: {$asset->value}\n";
    }

    echo "\n----------------------------------------\n";
    echo 'Vulnerabilities ('.count($control->vulnerabilities)."):\n";
    foreach ($control->vulnerabilities as $vuln) {
        echo "  - ID: {$vuln->id} | Asset: {$vuln->asset} ({$vuln->assetValue}) | Title: {$vuln->title} | Severity: {$vuln->severity}\n";
    }
    echo "========================================\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
