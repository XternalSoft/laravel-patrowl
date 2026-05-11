<?php

declare(strict_types=1);

require_once __DIR__.'/../vendor/autoload.php';

use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

/**
 * Example script to list risks using the LaravelPatrowl connector in CSV format.
 *
 * Usage:
 * PATROWL_TOKEN=your_token PATROWL_ORG_ID=your_org_id php examples/list_risks.php
 */

// Configuration
$token = getenv('PATROWL_TOKEN') ?: 'YOUR_API_TOKEN';
$orgId = getenv('PATROWL_ORG_ID') ?: null;
$baseUrl = getenv('PATROWL_BASE_URL') ?: 'https://dashboard.cloud.patrowl.io/api/auth';

if ($token === 'YOUR_API_TOKEN') {
    fwrite(STDERR, "Please provide your API token via PATROWL_TOKEN environment variable.\n");
    exit(1);
}

$connector = new LaravelPatrowl(
    apiToken: $token,
    baseUrl: $baseUrl,
    defaultOrganizationId: $orgId ? (int) $orgId : null
);

$out = fopen('php://stdout', 'w');

// Header
fputcsv($out, ['id', 'title', 'topic', 'topic_id', 'topic_slug', 'subtopic', 'subtopic_id', 'subtopic_slug', 'asset', 'asset_tags', 'severity', 'status', 'created_at', 'last_seen_at', 'raw_data']);

try {
    $paginator = $connector->risks()->all();

    foreach ($paginator->items() as $risk) {
        /** @var Xternalsoft\LaravelPatrowl\Data\RiskInListData $risk */
        fputcsv($out, [
            $risk->id,
            $risk->title,
            $risk->topic ?? '',
            $risk->topicId ?? '',
            $risk->topicSlug ?? '',
            $risk->subtopic ?? '',
            $risk->subtopicId ?? '',
            $risk->subtopicSlug ?? '',
            $risk->assetValue ?? '',
            implode('|', $risk->assetTags ?? []),
            $risk->severity?->name ?? '',
            $risk->status?->value ?? '',
            $risk->createdAt ?? '',
            $risk->lastSeenAt ?? '',
            $risk->description ?? '',
        ]);
    }

    fclose($out);
} catch (Exception $e) {
    fwrite(STDERR, 'Error: '.$e->getMessage()."\n");
    exit(1);
}
