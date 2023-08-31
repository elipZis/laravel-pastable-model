<?php

namespace ElipZis\Pastable\Commands;

use ElipZis\Pastable\Jobs\PastableJob;
use Illuminate\Console\Command;

/**
 *
 */
class PastableCommand extends Command
{
    /**
     * @var string
     */
    public $signature = 'pastable:all';

    /**
     * @var string
     */
    public $description = 'Trigger the cut/copy & pasting of all implementing classes';

    /**
     * @return int
     */
    public function handle(): int
    {
        PastableJob::dispatch();

        return self::SUCCESS;
    }
}
