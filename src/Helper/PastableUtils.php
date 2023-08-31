<?php

namespace ElipZis\Pastable\Helper;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;

trait PastableUtils
{
    public function getPastableClasses(string $traitClass): array
    {
        $appNamespace = Application::getInstance()->getNamespace();

        return collect(File::allFiles(app_path()))->map(static function ($item) use ($appNamespace) {
            $rel = $item->getRelativePathName();

            return sprintf('%s%s', $appNamespace, implode('\\', explode('/', substr($rel, 0, strrpos($rel, '.')))));
        })->filter(fn ($class) => class_exists($class))
            ->filter(fn ($class) => in_array($traitClass, class_uses_recursive($class)))
            ->all();
    }
}
