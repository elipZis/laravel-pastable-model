<?php

namespace ElipZis\Pastable\Jobs;

use Carbon\Carbon;
use ElipZis\Pastable\Helper\PastableLogger;
use ElipZis\Pastable\Jobs\Middleware\AtomicJob;
use ElipZis\Pastable\Models\Traits\CopyPastable;
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
            /** @var CopyPastable $instance */
            $instance = new $this->class;
            $affected = $instance->copyAndPaste();

            $this->log("Copy & pasted {$affected} entries for class `{$this->class}` at {$now->toString()}");

            //Self-dispatch as long as there is more
            if ($affected > 0) {
                static::dispatch($this->class);
            }
        } catch (Throwable $t) {
            $this->log('Error while copy & pasting: '.$t->getMessage());
        }
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new AtomicJob($this->class)];
    }
}
