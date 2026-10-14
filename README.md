# A clean, fluent, and developer-friendly PHP wrapper for the Patrowl API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/xternalsoft/laravel-patrowl.svg?style=flat-square)](https://packagist.org/packages/xternalsoft/laravel-patrowl)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/xternalsoft/laravel-patrowl/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/xternalsoft/laravel-patrowl/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/xternalsoft/laravel-patrowl/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/xternalsoft/laravel-patrowl/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/xternalsoft/laravel-patrowl.svg?style=flat-square)](https://packagist.org/packages/xternalsoft/laravel-patrowl)

A clean, fluent, and developer-friendly PHP wrapper for the Patrowl API. This package allows you to easily interact with assets, asset groups, tags, risks, controls, organizations and EASM resources (domains, IPs, services, CVEs…) of the Patrowl platform from your Laravel application.

## Requirements

- PHP 8.4+
- Laravel 12 or 13
- A Patrowl API token (Patrowl API 2.38+)

## Installation

You can install the package via composer:

```bash
composer require xternalsoft/laravel-patrowl
```

Publish the config file with:

```bash
php artisan patrowl:install
# or
php artisan vendor:publish --tag="laravel-patrowl-config"
```

This is the contents of the published config file:

```php
return [
    'api_token' => env('PATROWL_API_TOKEN'),
    'base_url' => env('PATROWL_API_BASE_URL', 'https://dashboard.cloud.patrowl.io/api/auth'),
    'dashboard_url' => env('PATROWL_DASHBOARD_URL', 'https://dashboard.cloud.patrowl.io'),
    'timeout' => 30,
    'default_organization_id' => env('PATROWL_DEFAULT_ORGANIZATION_ID'),
    'limit' => env('PATROWL_PAGINATION_LIMIT', 100),
];
```


## Usage

### Configuration

Add your Patrowl settings to your `.env` file:

```env
PATROWL_API_TOKEN=your-api-token
PATROWL_API_BASE_URL=https://dashboard.cloud.patrowl.io/api/auth
PATROWL_DEFAULT_ORGANIZATION_ID=1
PATROWL_PAGINATION_LIMIT=100
```

| Variable | Description |
|---|---|
| `PATROWL_API_TOKEN` | **Required.** Your API token. A `MissingApiTokenException` is thrown when it is missing. |
| `PATROWL_API_BASE_URL` | API base URL, defaults to the Patrowl cloud. |
| `PATROWL_DEFAULT_ORGANIZATION_ID` | Organization sent with most requests (`org_id` / `organization`). Many endpoints answer `403` without it: set it to one of the IDs returned by `LaravelPatrowl::organizations()->all()`. |
| `PATROWL_PAGINATION_LIMIT` | Page size used by auto-paginated lists (default `100`). |

### Basic usage

Use the `LaravelPatrowl` facade:

```php
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;

foreach (LaravelPatrowl::assets()->all()->items() as $asset) {
    echo $asset->value;
}
```

Outside of Laravel's container (scripts, multiple tokens…), instantiate the connector directly:

```php
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

$patrowl = new LaravelPatrowl(
    apiToken: 'your-api-token',
    baseUrl: 'https://dashboard.cloud.patrowl.io/api/auth',
    defaultOrganizationId: 1, // optional
    limit: 100,               // optional, page size
    timeout: 30               // optional, seconds
);

$asset = $patrowl->assets()->get(42);
```

### Pagination

Every `all()` method (except `organizations()->all()`) returns a Saloon `Paginator` that fetches the next pages automatically. Iterate over `items()` to get DTOs:

```php
$paginator = LaravelPatrowl::assets()->all(['search' => 'example']);

foreach ($paginator->items() as $asset) {
    // AssetInListData
}

// Or as a Laravel LazyCollection
$values = $paginator->collect()->map(fn ($asset) => $asset->value);
```

> Iterating over the paginator itself (`foreach ($paginator as $page)`) yields raw `Response` objects, one per page.

Filters are passed as query parameters (the first argument of `all()`), using the Patrowl API names.

### Error handling

- Methods returning a DTO throw a `LogicException` when the API answers with an error. The original Saloon `RequestException` (with the response) is available via `$e->getPrevious()`.
- Paginators throw a Saloon `RequestException` (e.g. `ForbiddenException`) when a page fails.
- Methods returning a `Saloon\Http\Response` (`delete()`, `addTag()`, `syncTags()`…) do not throw: check `$response->successful()` / `$response->failed()`.

```php
use Saloon\Exceptions\Request\RequestException;

try {
    $asset = LaravelPatrowl::assets()->get($assetId);
} catch (LogicException $e) {
    $previous = $e->getPrevious();

    if ($previous instanceof RequestException) {
        $status = $previous->getResponse()->status();
    }
}
```

### Assets

#### List and get assets

```php
// Auto-paginated list of AssetInListData
foreach (LaravelPatrowl::assets()->all(['type' => 'domain'])->items() as $asset) {
    echo $asset->value;
    echo $asset->thumbnail?->url;

    foreach ($asset->relatedTechnologies ?? [] as $technology) {
        echo "{$technology->vendor} {$technology->product} {$technology->version}";
    }
}

// AssetData in detail (owners, tags, groups, screenshot...)
$asset = LaravelPatrowl::assets()->get($assetId);

echo $asset->criticality->name;
echo $asset->screenshot?->url;
```

#### Create an asset

```php
use Xternalsoft\LaravelPatrowl\Data\CreateAssetData;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;
use Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException;

$data = new CreateAssetData(
    value: 'example.com',
    organization: 1, // defaults to PATROWL_DEFAULT_ORGANIZATION_ID
    description: 'My new asset',
    criticality: ComplexityEnum::High,
    tags: [12, 34],
    owners: [5]
);

try {
    $asset = LaravelPatrowl::assets()->create($data);
} catch (AssetAlreadyExistsException $e) {
    $existingAssetId = $e->response->json('data.id');
}
```

> Creating an asset from a CIDR range (subnet) is not supported yet: the API returns an asset group instead of an asset.

#### Update and delete an asset

```php
// Partial update (PATCH), returns the updated AssetData
$asset = LaravelPatrowl::assets()->update($assetId, [
    'description' => 'Updated description',
    'criticality' => 3,
]);

// Update the criticality of several assets at once
$response = LaravelPatrowl::assets()->bulkUpdate([
    'asset_ids' => [1, 2, 3],
    'criticality' => 2,
]);

// Delete
$response = LaravelPatrowl::assets()->delete($assetId);

// Recompute the asset score
$response = LaravelPatrowl::assets()->refreshScore($assetId);
```

#### Manage asset tags

```php
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;

// Create a tag and link it to the asset (POST /tags/)
$response = LaravelPatrowl::assets()->addTag($assetId, new AddTagToAssetData(
    value: 'production',
    organization: 1
));

// Replace all the tags of an asset by a list of tag IDs (an empty array removes all tags)
$response = LaravelPatrowl::assets()->syncTags($assetId, [12, 34]);

// Remove one tag
$response = LaravelPatrowl::assets()->removeTag($assetId, $tagId);
```

### Asset groups

#### List and get asset groups

```php
// Auto-paginated list of AssetGroupInListData
foreach (LaravelPatrowl::assetGroups()->all()->items() as $group) {
    echo $group->title;
    echo $group->vulnsBySeverity['critical'] ?? 0;
}

// AssetGroupData in detail
$group = LaravelPatrowl::assetGroups()->get($groupId);

echo $group->counts['assets'] ?? 0;
```

#### Create an asset group

```php
use Xternalsoft\LaravelPatrowl\Data\CreateAssetGroupData;
use Xternalsoft\LaravelPatrowl\Enums\ComplexityEnum;

$group = LaravelPatrowl::assetGroups()->create(new CreateAssetGroupData(
    title: 'My Production Assets',
    description: 'Assets in production environment',
    organization: 1, // defaults to PATROWL_DEFAULT_ORGANIZATION_ID
    criticality: ComplexityEnum::High, // required by the API, defaults to Medium
    assets: [123, 456] // added right after creation
));
```

#### Update and delete an asset group

```php
// Partial update (PATCH), returns the updated AssetGroupData
$group = LaravelPatrowl::assetGroups()->update($groupId, [
    'title' => 'Renamed group',
]);

$response = LaravelPatrowl::assetGroups()->delete($groupId);
```

#### Manage the assets and tags of a group

```php
use Xternalsoft\LaravelPatrowl\Data\AddTagToAssetData;

$response = LaravelPatrowl::assetGroups()->addAssets($groupId, [123, 456]);
$response = LaravelPatrowl::assetGroups()->removeAssets($groupId, [456]);

$response = LaravelPatrowl::assetGroups()->addTag($groupId, new AddTagToAssetData(
    value: 'critical-infrastructure',
    organization: 1
));
```

### Tags

#### List tags

`tags()` lists the tags of the organization (`TagData`) with a preview of their linked assets and asset groups:

```php
foreach (LaravelPatrowl::assetTags()->tags(['search' => 'prod'])->items() as $tag) {
    echo $tag->value;

    foreach ($tag->assets as $asset) {
        echo $asset['value'];
    }
}
```

#### Bulk associate / dissociate tags

```php
use Xternalsoft\LaravelPatrowl\Data\BulkAssociateTagsData;

$response = LaravelPatrowl::assetTags()->associate(new BulkAssociateTagsData(
    assetIds: [2635579],
    tagIds: [4241],
    organizationId: 1815 // defaults to PATROWL_DEFAULT_ORGANIZATION_ID
));

echo $response->json('message'); // "Associated 1 tags with 1 assets."

$response = LaravelPatrowl::assetTags()->dissociate(
    assetIds: [2635579],
    tagIds: [4241]
);

echo $response->json('message'); // "Dissociated 1 tags from 1 assets."
```

### Risks

```php
// Auto-paginated list of RiskInListData
foreach (LaravelPatrowl::risks()->all(['severity' => 4])->items() as $risk) {
    echo $risk->title.' ('.$risk->severity->name.')';
}

// RiskData in detail
$risk = LaravelPatrowl::risks()->get($riskId);

// Export to CSV (org_id is required by the API)
file_put_contents('risks.csv', LaravelPatrowl::risks()->exportCsv());

// Topics and subtopics
foreach (LaravelPatrowl::risks()->topics()->items() as $topic) {
    echo $topic->title.' ('.$topic->slug.')';
}

foreach (LaravelPatrowl::risks()->subtopics()->items() as $subtopic) {
    echo $subtopic->title.' ('.$subtopic->slug.')';
}
```

### Controls

```php
use Xternalsoft\LaravelPatrowl\Enums\ControlResultEnum;
use Xternalsoft\LaravelPatrowl\Enums\ControlStatusEnum;

// Auto-paginated list of ControlLiteData, filtered
$controls = LaravelPatrowl::controls()->all([
    'severity' => 'critical',
    'status' => ControlStatusEnum::Finished->value,
]);

foreach ($controls->items() as $control) {
    if ($control->result === ControlResultEnum::Impacted) {
        // Handle impacted asset controls
    }
}

// ControlData in detail (impacted assets and vulnerabilities)
$control = LaravelPatrowl::controls()->get($controlId);

foreach ($control->assetsImpacted as $asset) {
    echo $asset->value;
}
```

### Organizations

```php
// The organizations you belong to (OrganizationLiteData: id, name, slug, tenantId, tenantName).
// Not paginated: returns an array.
foreach (LaravelPatrowl::organizations()->all() as $organization) {
    echo "{$organization->id}: {$organization->name} ({$organization->tenantName})";
}

// OrganizationData in detail (identity, members, profile, features, credits, monitoring)
$organization = LaravelPatrowl::organizations()->get($organizationId);

echo $organization->credits->easm->available;
```

### EASM (Attack Surface Management)

Every EASM resource exposes the same read API: `all()` (auto-paginated) and `get()`. DTOs live in `Xternalsoft\LaravelPatrowl\Data\Easm` and match the API payloads exactly, including nested objects.

| Method | Endpoint | `all()` DTO | `get()` DTO |
|---|---|---|---|
| `domains()` | `/domains` | `DomainData` | `DomainData` |
| `ipv4s()` | `/ipv4s` | `Ipv4Data` | `Ipv4Data` |
| `resolutions()` | `/resolutions` | `ResolutionData` | `ResolutionData` |
| `asns()` | `/asns` | `AsnData` | `AsnData` |
| `services()` | `/services` | `ServiceData` | `ServiceDetailData` (with `banner`) |
| `webServers()` | `/webservers` | `WebServerData` | `WebServerData` |
| `certificates()` | `/certificates` | `CertificateData` | `CertificateData` |
| `vendors()` | `/vendors` | `VendorData` | `VendorData` |
| `products()` | `/products` | `ProductData` | `ProductData` |
| `technologies()` | `/technologies` | `TechnologyData` | `TechnologyData` |
| `technologyDetections()` | `/technology-detections` | `TechnologyDetectionData` | `TechnologyDetectionData` |
| `cves()` | `/cves` | `CveData` | `CveDetailData` (per-version CVSS, EPSS, exploits) |

```php
// Apex domains only, filters are passed as query parameters
foreach (LaravelPatrowl::domains()->all(['is_top_domain' => true])->items() as $domain) {
    echo $domain->value;

    foreach ($domain->dnsRecords as $record) {
        echo "{$record->recordType} {$record->value}";
    }
}

// Open TCP services, with their certificate and web servers
foreach (LaravelPatrowl::services()->all(['state' => 'open', 'protocol' => 'tcp'])->items() as $service) {
    echo "{$service->value}:{$service->portNumber}";
    echo $service->certificate?->isExpired ? 'expired certificate' : '';

    foreach ($service->webservers as $webServer) {
        echo $webServer->url;
    }
}

$service = LaravelPatrowl::services()->get($serviceId);
echo $service->banner;
```

CVEs are cursor paginated (handled transparently) and retrieved by identifier:

```php
foreach (LaravelPatrowl::cves()->all(['severity' => 'critical,high', 'is_kev' => true])->items() as $cve) {
    echo "{$cve->identifier} {$cve->cvss?->score}";
}

$cve = LaravelPatrowl::cves()->get('CVE-2026-12345');

if ($cve->threatInfo?->isKev) {
    foreach ($cve->exploits as $exploit) {
        echo $exploit->reference;
    }
}
```

The default organization ID is sent as `org_id` when configured. Vendors and products are global catalogs and ignore it.

### Testing your code

The connector uses Saloon, so you can fake the API in your own tests:

```php
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;
use Xternalsoft\LaravelPatrowl\Requests\Assets\GetAssetRequest;

LaravelPatrowl::withMockClient(new MockClient([
    GetAssetRequest::class => MockResponse::make(['id' => 1, 'value' => 'example.com', /* ... */]),
]));
```

### Examples

Runnable scripts for every resource are available in the [`examples`](examples/README.md) directory.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

Upgrading from v1? See the [upgrade guide](UPGRADE.md).

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Parrot](https://github.com/XternalSoft)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
