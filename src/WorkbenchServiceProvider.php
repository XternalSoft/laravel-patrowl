<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl;

use Illuminate\Console\Events\CommandStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

final class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Event::listen(CommandStarting::class, function ($event) {
            if (str_starts_with($event->command, 'boost:')) {
                // Make sure this actually points to your package root!
                app()->setBasePath(realpath(__DIR__.'/../../../'));
                app()->useAppPath(base_path('src'));

                config()->set('boost.code_environments.claude_code.guidelines_path', base_path('CLAUDE.md'));
            }
        });
    }

    public function boot(): void {}
}
