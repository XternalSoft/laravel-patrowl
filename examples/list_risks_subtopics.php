<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list risk subtopics using the LaravelPatrowl connector.
 *
 * Usage:
 * PATROWL_TOKEN=your_token PATROWL_ORG_ID=your_org_id php examples/list_risks_subtopics.php
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
    echo "--- RISK SUBTOPICS ---\n";
    $subtopics = $connector->risks()->subtopics();

    foreach ($subtopics->items() as $subtopic) {
        /** @var Xternalsoft\LaravelPatrowl\Data\RiskSubtopicData $subtopic */
        echo "ID: {$subtopic->id}\n";
        echo "Title: {$subtopic->title}\n";
        echo "Slug: {$subtopic->slug}\n";

        if ($subtopic->description) {
            echo "Description: {$subtopic->description}\n";
        }

        if ($subtopic->isAvailable !== null) {
            echo 'Available: '.($subtopic->isAvailable ? 'Yes' : 'No')."\n";
        }

        if ($subtopic->defaultSeverity !== null) {
            echo "Default Severity: {$subtopic->defaultSeverity}\n";
        }

        if ($subtopic->securityCheck !== null) {
            echo "Security Check: {$subtopic->securityCheck}\n";
        }

        if ($subtopic->remediation) {
            echo "Remediation: {$subtopic->remediation}\n";
        }

        if ($subtopic->remediationEffort !== null) {
            echo "Effort: {$subtopic->remediationEffort}\n";
        }

        if ($subtopic->remediationPriority !== null) {
            echo "Priority: {$subtopic->remediationPriority}\n";
        }

        echo str_repeat('-', 20)."\n";
    }

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    exit(1);
}
