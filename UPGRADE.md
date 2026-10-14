# Upgrade Guide

## From v1 to v2

v2 aligns the package with Patrowl API **2.36 → 2.38**. Several endpoints were removed or reshaped by Patrowl, so a few package methods changed accordingly.

### High impact

#### `organizations()->default()` was removed

The Patrowl API no longer exposes `GET /orgs/default/`. Fetch an organization by its ID instead:

```php
// v1
$org = LaravelPatrowl::organizations()->default();

// v2
$org = LaravelPatrowl::organizations()->get($organizationId);
```

You can find your organization IDs with `organizations()->all()` (see below).

#### `organizations()->all()` returns an array of `OrganizationLiteData`

`GET /orgs/` is no longer paginated and returns a lighter payload. The method now returns `array<int, OrganizationLiteData>` instead of a `Paginator` of `OrganizationData`.

```php
// v1
foreach (LaravelPatrowl::organizations()->all()->items() as $org) {
    echo $org->identity->name;
}

// v2
foreach (LaravelPatrowl::organizations()->all() as $org) {
    echo $org->name;
}
```

`OrganizationLiteData` exposes `id`, `name`, `slug`, `tenantId` and `tenantName`. For the full details (members, features, credits…), call `organizations()->get($org->id)`.

The `limit` and `page` query parameters are no longer sent for this endpoint.

#### `assets()->replace()` was removed

`POST /assets/{id}/` was removed from the API. Use `update()`, which sends a `PATCH`:

```php
// v1
LaravelPatrowl::assets()->replace($id, $data);

// v2
LaravelPatrowl::assets()->update($id, $data);
```

`ReplaceAssetRequest` was deleted as well.

#### `assetTags()->all()` and `assetTags()->get()` were removed

`GET /assets/tags/` and `GET /assets/tags/{id}/` no longer exist (the API answers `403` / `404`). Use `assetTags()->tags()` to list the tags of the organization:

```php
// v1
LaravelPatrowl::assetTags()->all();

// v2
LaravelPatrowl::assetTags()->tags();
```

`GetAssetTagsRequest` and `GetAssetTagRequest` were deleted as well.

#### Set a default organization ID

Many Patrowl endpoints now reject requests that do not target an organization you belong to (HTTP 403 `You do not have permission to perform this action.`). Make sure `PATROWL_DEFAULT_ORGANIZATION_ID` is set to one of the IDs returned by `organizations()->all()`:

```dotenv
PATROWL_DEFAULT_ORGANIZATION_ID=1
```

### Medium impact

#### `assets()->create()` throws when the asset already exists

When the asset already exists, the API answers `208 Already Reported`. The package now throws `Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException`. The raw response is available on the exception:

```php
use Xternalsoft\LaravelPatrowl\Exceptions\AssetAlreadyExistsException;

try {
    $asset = LaravelPatrowl::assets()->create($data);
} catch (AssetAlreadyExistsException $e) {
    $existingId = $e->response->json('data.id');
}
```

Creating an asset from a CIDR range (subnet) is not supported by `create()` yet: the API returns an asset group instead of an asset.

#### `CreateAssetData::$suborganizations` was replaced by `$teams`

The API no longer accepts `suborganizations` (nor `org_id`) when creating an asset. The organization is now sent as `organization`, and the asset can be assigned to teams:

```php
// v1
new CreateAssetData(value: 'example.com', suborganizations: [3]);

// v2
new CreateAssetData(value: 'example.com', teams: [3]);
```

#### `AssetInListData::$relatedTechnologies` is now an array

It used to be a single `RelatedTechnologyData`. It is now `array<int, RelatedTechnologyData>|null`:

```php
// v1
$asset->relatedTechnologies?->product;

// v2
foreach ($asset->relatedTechnologies ?? [] as $technology) {
    echo $technology->product;
}
```

#### `CreateAssetGroupData::$criticality` is no longer nullable

The API now requires a criticality when creating an asset group. The property is typed `ComplexityEnum` and defaults to `ComplexityEnum::Medium`. Passing `null` explicitly is no longer allowed.

### Low impact (additions)

These are new, backward compatible fields:

| DTO | New property | Type |
|---|---|---|
| `OrganizationIdentityData` | `tenantId` | `?int` |
| `AssetData` | `screenshot` | `?AssetScreenshotData` (`url`, `takenAt`) |
| `AssetInListData` | `thumbnail` | `?AssetScreenshotData` (`url`, `takenAt`) |
| `AssetGroupData` | `counts` | `?array{assets, vulnerabilities, risks, assets_with_risks}` |
| `AssetGroupInListData` | `vulnsBySeverity` | `?array{critical, high, medium, low, info}` |

If you extend or construct these DTOs positionally, note that the new properties were appended at the end of each constructor.
