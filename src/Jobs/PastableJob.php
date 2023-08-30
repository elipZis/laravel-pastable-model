<?php

namespace ElipZis\Pastable\Jobs;

use ElipZis\Pastable\Helper\PastableLogger;
use ElipZis\Pastable\Models\Traits\CopyPastable;
use ElipZis\Pastable\Models\Traits\CutPastable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\File;

class PastableJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, PastableLogger;

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
        //Get all classes implementing our traits
        $copyClasses = $this->getPastableClasses(CopyPastable::class);
        $cutClasses = $this->getPastableClasses(CutPastable::class);

        if (empty($copyClasses) && empty($cutClasses)) {
            $this->log('No pasteable classes found.');
            return;
        }

        $count = count($copyClasses) + count($cutClasses);
        $this->log("Found {$count} pasteable classes.");

        foreach ($copyClasses as $copyClass) {
            //Copy & Paste every class
//            /** @var CopyPastable $instance */
//            $instance = new $class;
//            $instance->paste();
        }

        foreach ($cutClasses as $cutClass) {
            //Cut & Paste every class
//            /** @var CutPastable $instance */
//            $instance = new $class;
//            $instance->paste();
        }
    }

    /**
     * @param string $traitClass
     * @return array
     */
    public function getPastableClasses(string $traitClass): array
    {
        $appNamespace = Application::getInstance()->getNamespace();

        return collect(File::allFiles(app_path()))->map(static function ($item) use ($appNamespace) {
            $rel = $item->getRelativePathName();

            return sprintf('%s%s', $appNamespace, implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));
        })->filter(fn($class) => class_exists($class))
            ->filter(fn($class) => in_array($traitClass, class_uses_recursive($class)))
            ->all();
    }
}
