<?php

namespace ElipZis\Pastable\Jobs;

use ElipZis\Pastable\Helper\PastableLogger;
use ElipZis\Pastable\Helper\PastableUtils;
use ElipZis\Pastable\Models\Traits\CopyPastable;
use ElipZis\Pastable\Models\Traits\CutPastable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

/**
 * Fire all pastable classes
 */
class PastableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, PastableLogger, PastableUtils;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Get all classes implementing our traits
        $copyClasses = $this->getPastableClasses(CopyPastable::class);
        $cutClasses = $this->getPastableClasses(CutPastable::class);

        if (empty($copyClasses) && empty($cutClasses)) {
            $this->log('No pastable classes found.');

            return;
        }

        $count = count($copyClasses) + count($cutClasses);
        $this->log("Found {$count} pastable classes.");

        foreach ($copyClasses as $copyClass) {
            //Copy & Paste every class
            CopyPastableJob::dispatch($copyClass);
        }

        foreach ($cutClasses as $cutClass) {
            //Cut & Paste every class
            CutPastableJob::dispatch($cutClass);
        }
    }
}
