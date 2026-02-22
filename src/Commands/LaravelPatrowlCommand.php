<?php

declare(strict_types=1);

namespace Xternalsoft\LaravelPatrowl\Commands;

use Illuminate\Console\Command;

final class LaravelPatrowlCommand extends Command
{
    public $signature = 'laravel-patrowl';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
