<?php

namespace AliSaleem\LaravelSettings\Tests;

use AliSaleem\LaravelSettings\LaravelSettingsServiceProvider;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
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
}
