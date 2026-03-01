<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

final class LaravelPatrowlServiceProvider extends PackageServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->singleton(LaravelPatrowl::class, function () {
            return new LaravelPatrowl(
                apiToken: config('patrowl.api_token'),
                baseUrl: config('patrowl.base_url'),
                defaultOrganizationId: config('patrowl.default_organization_id') ? (int) config('patrowl.default_organization_id') : null,
                limit: (int) config('patrowl.limit', 100),
                timeout: (int) config('patrowl.timeout', 30)
            );
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-patrowl')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command->publishConfigFile();
            });
    }
}
