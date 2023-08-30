<?php

namespace ElipZis\Pastable\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class CutPastableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;
}
