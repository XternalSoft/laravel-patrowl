<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list risk topics using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_TOKEN=your_token PATROWL_ORG_ID=your_org_id php examples/list_risks_topics.php
 */

// Configuration
$token = getenv('PATROWL_TOKEN') ?: 'YOUR_API_TOKEN';
$orgId = getenv('PATROWL_ORG_ID') ?: null;
$baseUrl = getenv('PATROWL_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if ($token === 'YOUR_API_TOKEN') {
    echo "Please provide your API token via PATROWL_TOKEN environment variable.\n";
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId ? (int) $orgId : null
);

try {
    echo "--- RISK TOPICS ---\n";
    $topics = $connector->risks()->topics();
    
    foreach ($topics->items() as $topic) {
        /** @var Xternalsoft\LaravelPatrowl\Data\RiskTopicData $topic */
        echo "ID: {$topic->id} | Title: {$topic->title} | Slug: {$topic->slug}\n";
    }

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
