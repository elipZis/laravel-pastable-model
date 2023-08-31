<?php

namespace ElipZis\Pastable\Jobs;

use Carbon\Carbon;
use ElipZis\Pastable\Helper\PastableLogger;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CopyPastableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, PastableLogger, Queueable, SerializesModels;

    public function __construct(protected string $class)
    {
    }

    /**
     * Cut/Copy & Paste in chunks
     *
     * @return void
     *
     * @throws Exception
     */
    public function handle()
    {
        $now = Carbon::now();
        $this->log("Starting copy & pasting for class `{$this->class}` at {$now->toString()}");

        try {
            $affected = (new $this->class)->copyAndPaste();

            $this->log("Copy & pasted {$affected} entries for class `{$this->class}` at {$now->toString()}");

        } catch (Throwable $t) {
            $this->log("Error while copy & pasting ({$t->getLine()}): {$t->getMessage()}");
        }
    }
}
