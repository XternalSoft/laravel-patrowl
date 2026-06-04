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
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/list_assets.php
```

#### 2. List Controls (with Auto-Pagination)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/list_controls.php
```

#### 3. Get Control Detail (with Related Assets & Vulnerabilities)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/get_control.php <control_id>
```

#### 4. List Warning Controls (Potentially Impacted only)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/list_warning_controls.php
```

#### 5. List Recent Controls (Started during the last hour)
```bash
PATROWL_API_TOKEN="your_token_here" php examples/list_recent_controls.php
```

### Risks Examples

These examples showcase risk and vulnerability management:

#### 6. List Risks
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/list_risks.php
```

#### 7. Export Risks to CSV
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/export_risks_csv.php
```

#### 8. List Risk Topics
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/list_risks_topics.php
```

#### 9. List Risk Subtopics
```bash
PATROWL_API_TOKEN=your_token_here PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id php examples/list_risks_subtopics.php
```

## Usage in a Laravel Application

If you have already configured your `.env` file with:

```env
PATROWL_API_TOKEN=your_token
PATROWL_DEFAULT_ORGANIZATION_ID=your_org_id
```

You can use the Facade or the container to interact with the API:

### Using the Facade
```php
use Xternalsoft\LaravelPatrowl\Facades\LaravelPatrowl;

$assets = LaravelPatrowl::assets()->all();
foreach ($assets->items() as $asset) {
    // ...
}
```

### Using Dependency Injection
```php
use Xternalsoft\LaravelPatrowl\LaravelPatrowl;

public function index(LaravelPatrowl $patrowl)
{
    $risks = $patrowl->risks()->all();
    return view('risks.index', [
        'risks' => $risks->collect()
    ]);
}
```

### Using Tinker
```bash
php artisan tinker

> $patrowl = app(Xternalsoft\LaravelPatrowl\LaravelPatrowl::class);
> $patrowl->assets()->all()->collect();
```
