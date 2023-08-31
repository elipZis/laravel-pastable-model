<?php

namespace ElipZis\Pastable\Helper;

use Illuminate\Support\Facades\Log;

trait PastableLogger
{
    protected function log(string $message, string $level = 'debug'): void
    {
        if (!config('pastable.logging.enabled', false)) {
            return;
        }

        $level = config('pastable.logging.level') ?? $level;
        Log::log($level, "[Pastable] {$message}");
    }
}
