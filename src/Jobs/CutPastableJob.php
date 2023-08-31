<?php

namespace ElipZis\Pastable\Jobs;

use Carbon\Carbon;
use ElipZis\Pastable\Helper\PastableLogger;
use ElipZis\Pastable\Jobs\Middleware\AtomicJob;
use ElipZis\Pastable\Models\Traits\CutPastable;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CutPastableJob implements ShouldQueue
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
        $this->log("Starting cut & pasting for class `{$this->class}` at {$now->toString()}");

        try {
            /** @var CutPastable $instance */
            $instance = new $this->class;
            $affected = $instance->cutAndPaste();

            $this->log("Cut & pasted {$affected} entries for class `{$this->class}` at {$now->toString()}");

            //Self-dispatch as long as there is more
            if ($affected > 0) {
                static::dispatch($this->class);
            }
        } catch (Throwable $t) {
            $this->log('Error while cut & pasting: '.$t->getMessage());
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
