<?php

namespace ElipZis\Pastable\Commands;

use ElipZis\Pastable\Helper\PastableUtils;
use ElipZis\Pastable\Jobs\CutPastableJob;
use ElipZis\Pastable\Models\Traits\CutPastable;
use Illuminate\Console\Command;

/**
 *
 */
class CutPastableCommand extends Command
{
    use PastableUtils;

    /**
     * @var string
     */
    public $signature = 'pastable:cut';

    /**
     * @var string
     */
    public $description = 'Trigger the cut & pasting of all implementing classes';

    /**
     * @return int
     */
    public function handle(): int
    {
        $classes = $this->getPastableClasses(CutPastable::class);
        foreach ($classes as $class) {
            CutPastableJob::dispatch($class);
        }

        return self::SUCCESS;
    }
}
