<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=1 php examples/list_controls.php\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    echo "Fetching controls...\n";
    $controls = $client->controls()->all();

    foreach ($controls->items() as $control) {
        $statusValue = $control->status instanceof UnitEnum ? $control->status->name : $control->status;
        $resultValue = $control->result instanceof UnitEnum ? $control->result->name : $control->result;

        echo "----------------------------------------\n";
        echo "ID:          {$control->id}\n";
        echo "Title:       {$control->title}\n";
        echo "Status:      {$statusValue}\n";
        echo "Result:      {$resultValue}\n";
        echo "Severity:    {$control->severity}\n";
        echo 'Description: '.mb_substr((string) $control->description, 0, 100)."...\n";
    }
    echo "----------------------------------------\n";
    echo "Done.\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
