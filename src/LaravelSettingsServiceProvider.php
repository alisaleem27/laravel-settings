<?php

namespace AliSaleem\LaravelSettings;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSettingsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('settings')
            ->hasConfigFile();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Settings::class, function () {
            $settingsClass = config('settings.provider');
            return new $settingsClass();
        });
    }
}
