<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\Enums\ControlResultEnum;
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$token = getenv('PATROWL_API_TOKEN');
$baseUrl = getenv('PATROWL_API_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if (! $token) {
    echo "Error: PATROWL_API_TOKEN environment variable is not set.\n";
    echo "Usage: PATROWL_API_TOKEN=your_token php examples/controls_warning_by_organization.php\n";
    exit(1);
}

try {
    $client = new LaravelPatrowl(apiToken: $token, baseUrl: $baseUrl);

    foreach ($client->organizations()->all() as $organization) {
        echo "\n=== {$organization->name} (org {$organization->id}) ===\n";

        $controls = $client->controls()->all([
            'result' => ControlResultEnum::PotentiallyImpacted->value,
            'org_id' => $organization->id,
        ]);

        $count = 0;
        foreach ($controls->items() as $control) {
            $count++;
            echo "[{$control->id}] {$control->title}\n";

            foreach ($client->controls()->assets($control->id, ['org_id' => $organization->id])->items() as $asset) {
                echo "    - {$asset->value} ({$asset->controlImpact})\n";
            }
        }

        echo "{$count} warning controls.\n";
    }

    echo "\nDone.\n";
} catch (Exception $e) {
    echo 'Request failed: '.$e->getMessage()."\n";
    exit(1);
}
