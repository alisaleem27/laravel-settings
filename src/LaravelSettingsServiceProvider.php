<?php

declare(strict_types=1);

namespace AliSaleem\LaravelSettings;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSettingsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('settings')
            ->hasConfigFile()
            ->hasCommand(InitializeSettingsCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(
            config('settings.class'),
            fn () => with(
                config('settings.class'),
                fn ($class) => new $class(config('settings'))
            )
        );
    }
}
