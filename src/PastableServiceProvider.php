<?php

namespace ElipZis\Pastable;

use ElipZis\Pastable\Commands\PastableCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
