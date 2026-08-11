# LaravelPatrowl Examples

These scripts demonstrate how to use the `LaravelPatrowl` package to interact with the Patrowl API.

## Setup

Before running the scripts, ensure you have installed the dependencies:

```bash
composer install
```

## Running the Examples

All examples rely on environment variables for API configuration. You must provide your Patrowl API token (`PATROWL_API_TOKEN`) and optionally your default organization ID (`PATROWL_DEFAULT_ORGANIZATION_ID`) when running them.

### Assets & Controls Examples

These examples showcase managing assets and the new security controls integration:

#### 1. List Assets
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/assets_list.php
```

#### 2. Get Asset Detail (with Tags, Groups & Technologies)
```bash
PATROWL_API_TOKEN=your_token_here php examples/assets_get.php <asset_id>
```

#### 3. List Controls (with Auto-Pagination)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/controls_list.php
```

#### 4. Get Control Detail (with Related Assets & Vulnerabilities)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/controls_get.php <control_id>
```

#### 5. List Warning Controls (Potentially Impacted only)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/controls_warning_list.php
```

#### 6. List Recent Controls (Started during the last hour)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/controls_recent_list.php
```

### Risks Examples

These examples showcase risk and vulnerability management:

#### 7. List Risks
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/risks_list.php
```

#### 8. Export Risks to CSV
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/risks_export_csv.php
```

#### 9. List Risk Topics
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/risks_topic_list.php
```

#### 10. List Risk Subtopics
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/risks_subtopic_list.php
```

### Tags Examples

These examples showcase tag management (listing, creation, and bulk association):

#### 11. List Tags (with complete related assets & groups)
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/tags_list.php
```

#### 12. Associate Tags (bulk)
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/tags_associate.php <asset_id> <tag_id>
```

#### 13. Dissociate Tags (bulk)
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/tags_dissociate.php <asset_id> <tag_id>
```
