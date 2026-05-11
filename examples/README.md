# LaravelPatrowl Examples

These scripts demonstrate how to use the `LaravelPatrowl` package to interact with the Patrowl API.

## Setup

Before running the scripts, ensure you have installed the dependencies:

```bash
composer install
```

## Running the Examples

You can provide your Patrowl API token and Organization ID via environment variables:

### List Assets
```bash
PATROWL_TOKEN=your_token_here PATROWL_ORG_ID=your_org_id php examples/list_assets.php
```

### List Risks
```bash
PATROWL_TOKEN=your_token_here PATROWL_ORG_ID=your_org_id php examples/list_risks.php
```

### Export Risks to CSV
```bash
PATROWL_TOKEN=your_token_here PATROWL_ORG_ID=your_org_id php examples/export_risks_csv.php
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
