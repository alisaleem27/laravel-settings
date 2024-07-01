<?php

declare(strict_types=1);

namespace AliSaleem\LaravelSettings\Tests;

use AliSaleem\LaravelSettings\BaseSettings;
use AliSaleem\LaravelSettings\LaravelSettingsServiceProvider;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelSettingsServiceProvider::class,
        ];
    }

    public function storage(): Filesystem
    {
        return Storage::disk(config('settings.storage.disk'));
    }

    public function settings(): BaseSettings
    {
        return resolve(BaseSettings::class);
    }
}
