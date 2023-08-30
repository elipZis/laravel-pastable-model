<?php

namespace ElipZis\Pastable;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ElipZis\Pastable\Commands\PastableCommand;

class PastableServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-pastable-model')
            ->hasConfigFile('pastable')
            ->hasCommand(PastableCommand::class);
    }
}
