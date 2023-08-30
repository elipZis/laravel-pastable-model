<?php

namespace ElipZis\Pastable\Commands;

use Illuminate\Console\Command;

class PastableCommand extends Command
{
    public $signature = 'pastable:all';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
