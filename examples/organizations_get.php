<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/organizations_get.php [organization_id]\n";
    exit(1);
}

$orgId = isset($argv[1]) ? (int) $argv[1] : null;

$client = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl
);

try {
    if ($orgId) {
        echo "Fetching detailed organization ID: {$orgId}...\n";
        $org = $client->organizations()->get($orgId);
    } else {
        echo "Fetching default organization details...\n";
        $org = $client->organizations()->default();
    }

    echo "============================================================\n";
    echo "ORGANIZATION DETAILS\n";
    echo "============================================================\n";
    echo "ID:          {$org->identity->id}\n";
    echo "User ID:     {$org->identity->userId}\n";
    echo "Name:        {$org->identity->name}\n";
    echo "Slug:        {$org->identity->slug}\n";
    echo 'Active:      '.($org->identity->isActive ? 'Yes' : 'No')."\n";

    echo "\n--- Members ---\n";
    echo "Owner:       {$org->members->owner}\n";
    echo "User Count:  {$org->members->userCount}\n";

    echo "\n--- Profile ---\n";
    echo 'Is POC:      '.($org->profile->isPoc ? 'Yes' : 'No')."\n";
    echo "Created At:  {$org->profile->createdAt}\n";
    echo 'Contract End: '.($org->profile->endOfContract ?: 'None')."\n";

    echo "\n--- Features Status ---\n";
    echo 'SSO Enabled:                '.($org->features->isSso ? 'Yes' : 'No')."\n";
    echo 'Risk Insights Enabled:      '.($org->features->riskInsightsEnabled ? 'Yes' : 'No')."\n";
    echo 'Teams Enabled:              '.($org->features->teamsEnabled ? 'Yes' : 'No')."\n";
    echo 'Technologies Enabled:       '.($org->features->technologiesEnabled ? 'Yes' : 'No')."\n";
    echo 'Typosquatting Enabled:      '.($org->features->typosquattingEnabled ? 'Yes' : 'No')."\n";
    echo 'Auto EASM Enabled:          '.($org->features->autoEasm ? 'Yes' : 'No')."\n";
    echo 'Outside Business Hours:     '.($org->features->outsideBusinessHours ? 'Yes' : 'No')."\n";
    echo 'OBH Enabled:                '.($org->features->outsideBusinessHoursEnabled ? 'Yes' : 'No')."\n";

    echo "\n--- Credits ---\n";
    echo "EASM Credits:   {$org->credits->easm->available} available / {$org->credits->easm->limit} limit\n";
    echo "Pentest Credits: {$org->credits->pentest->available} available / {$org->credits->pentest->limit} limit\n";
    echo "Greybox Credits: {$org->credits->greybox}\n";

    echo "\n--- Monitoring ---\n";
    echo "Pentested Assets:                  {$org->monitoring->pentestedAssets}\n";
    echo "Rotation Frequency:                {$org->monitoring->rotationFrequency} days\n";
    echo 'Auto EASM Activation Consumption: '.($org->monitoring->autoEasmActivationConsumption ?? 'N/A')."\n";
    echo "============================================================\n";

} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
