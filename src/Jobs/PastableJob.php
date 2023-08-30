<?php

namespace ElipZis\Pastable\Jobs;

use ElipZis\Pastable\Models\Traits\CopyPastable;
use Illuminate\Bus\Queueable;
use Illuminate\Container\Container;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class PastableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //Get all classes implementing the CopyPasteable trait
        $classes = $this->getPasteableClasses();

        if (empty($classes)) {
            Log::info('[CopyPasteableJob] No pasteable classes found.');
            return;
        }

        Log::info('[CopyPasteableJob] Found ' . count($classes) . ' pasteable classes.');

        foreach ($classes as $class) {
            //Copy & Paste every class
            /** @var CopyPastable $instance */
            $instance = new $class;
            $instance->paste();
        }
    }

    public function getPasteableClasses(string $traitClass = CopyPastable::class): array
    {
        $appNamespace = Container::getInstance()->getNamespace();

        return collect(File::allFiles(app_path()))->map(static function ($item) use ($appNamespace) {
            $rel = $item->getRelativePathName();
            return sprintf('%s%s', $appNamespace, implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));
        })->filter(fn($class) => class_exists($class))
            ->filter(fn($class) => in_array($traitClass, class_uses_recursive($class)))
            ->all();
    }
}
