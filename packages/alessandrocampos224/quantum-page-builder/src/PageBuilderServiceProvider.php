<?php

namespace Quantum\Page\Builder\PageBuilder;

use Quantum\Page\Builder\PageBuilder\Commands\PageBuilderCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PageBuilderServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('quantum-page-builder')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_quantum_page_builder_table')
            ->hasCommand(PageBuilderCommand::class);
    }
}
