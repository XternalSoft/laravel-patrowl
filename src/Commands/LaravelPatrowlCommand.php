<?php

namespace Xternalsoft\LaravelPatrowl\Commands;

use Illuminate\Console\Command;

class LaravelPatrowlCommand extends Command
{
    public $signature = 'laravel-patrowl';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
