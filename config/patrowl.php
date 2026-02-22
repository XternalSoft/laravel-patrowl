<?php

return [
    'api_token' => env('PATROWL_API_TOKEN'),
    'base_url' => env('PATROWL_API_BASE_URL', 'https://dashboard.cloud.patrowl.io/api/auth'),
    'timeout' => 30,
    'default_organization_id' => env('PATROWL_DEFAULT_ORGANIZATION_ID'),
    'limit' => env('PATROWL_PAGINATION_LIMIT', 100),
];
