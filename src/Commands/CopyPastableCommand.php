<?php

namespace ElipZis\Pastable\Commands;

use ElipZis\Pastable\Helper\PastableUtils;
use ElipZis\Pastable\Jobs\CopyPastableJob;
use ElipZis\Pastable\Models\Traits\CopyPastable;
use Illuminate\Console\Command;

class CopyPastableCommand extends Command
{
    use PastableUtils;

    /**
     * @var string
     */
    public $signature = 'pastable:copy';

    /**
     * @var string
     */
    public $description = 'Trigger the copy & pasting of all implementing classes';

    /**
     */
    public function handle(): int
    {
        $classes = $this->getPastableClasses(CopyPastable::class);
        foreach ($classes as $class) {
            CopyPastableJob::dispatch($class);
        }

        return self::SUCCESS;
    }
}
