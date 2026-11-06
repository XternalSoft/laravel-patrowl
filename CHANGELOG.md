# Changelog

All notable changes to `laravel-patrowl` will be documented in this file.

## Unreleased

### Changed

- Aligned with Patrowl API **2.40.0** (minimum supported version is now 2.40).
- `assets()->syncTags()` now sends the tag IDs as `ids` (the API dropped `tags`).
- `risks()->get()` now sends `org_id` (required by the API) and accepts an optional `$orgId`, defaulting to the configured organization.
- `AssetData::$asset_owners` is now a non-nullable `array<AssetOwnerData>` (never `null` since API 2.39.1).
- `AssetInListData::$scoreLevel` is now `?int` (the API returns an integer).

### Removed

- Unused `AssetTagPolicyData` and `AssetTagPolicyFilterData` DTOs (no endpoint used them and they no longer matched the API).

### Notes

- The API no longer accepts `unknown` for the `service_state` filter of the assets endpoints.
