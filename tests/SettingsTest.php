<?php

use AliSaleem\LaravelSettings\Settings;

beforeEach(function () {
    $this->app->singleton(Settings::class, fn () => new class(config('settings')) extends Settings
    {
        public string $string = 'hello';

        public int $int = 123;
    });
});

it('can read config from storage', function () {
    $values = [
        'string' => 'random text',
        'int' => 456,
    ];

    $this->storage()->put(config('settings.storage.path'), json_encode($values));

    expect(settings()->string)->toBe($values['string']);
    expect(settings()->int)->toBe($values['int']);
});

it('can write config to storage', function () {
    settings()->string = 'random text';
    settings()->int = 456;

    settings()->persist();

    $values = json_decode($this->storage()->get(config('settings.storage.path')), true);
    expect($values)->toBe([
        'string' => 'random text',
        'int' => 456,
    ]);
});
