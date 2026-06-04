<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';
$orgId = getenv('PATROWL_DEFAULT_ORGANIZATION_ID') ? (int) getenv('PATROWL_DEFAULT_ORGANIZATION_ID') : null;

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token PATROWL_DEFAULT_ORGANIZATION_ID=1 php examples/list_recent_controls.php\n";
    exit(1);
}

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId
);

try {
    // Calculate 1 hour ago in ISO-8601 format
    $oneHourAgo = date(DateTimeInterface::ATOM, strtotime('-1 hour'));

    echo "Fetching controls started since: {$oneHourAgo} (last hour only)...\n";

    // Query using started_at__gte
    $controls = $client->controls()->all([
        'started_at__gte' => $oneHourAgo,
    ]);

    $count = 0;
    foreach ($controls->items() as $control) {
        $count++;
        $statusValue = $control->status instanceof UnitEnum ? $control->status->name : $control->status;
        $resultValue = $control->result instanceof UnitEnum ? $control->result->name : $control->result;

        echo "----------------------------------------\n";
        echo "ID:          {$control->id}\n";
        echo "Title:       {$control->title}\n";
        echo "Started At:  {$control->startedAt}\n";
        echo "Status:      {$statusValue}\n";
        echo "Result:      {$resultValue}\n";
        echo "Severity:    {$control->severity}\n";
    }

    echo "----------------------------------------\n";
    echo "Found {$count} controls in the last hour.\n";
    echo "Done.\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
