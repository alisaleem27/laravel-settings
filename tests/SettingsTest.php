<?php

declare(strict_types=1);

use AliSaleem\LaravelSettings\Settings;

beforeEach(function () {
    $this->app->singleton(config('settings.class'), fn () => new class(config('settings')) extends Settings
    {
        public string $string = '';

        public int $int = 0;

        public array $array = ['One', 'Two', 'Three'];

        public \Illuminate\Support\Collection $collection;
    });
});

it('can read config from storage', function () {
    $values = [
        'string' => 'random text',
        'int' => 123,
        'collection' => collect(['Five'])->toJson(),
    ];

    $this->storage()->put(config('settings.storage.path'), json_encode($values));

    expect($this->settings()->string)->toBe($values['string']);
    expect($this->settings()->int)->toBe($values['int']);
    expect($this->settings()->collection->toArray())->toBe(collect(['Five'])->toArray());
});

it('can write config to storage', function () {
    $this->settings()->string = 'random text';
    $this->settings()->int = 123;
    $this->settings()->array = ['Four'];
    $this->settings()->collection = collect(['Five']);

    $this->settings()->persist();

    $values = json_decode($this->storage()->get(config('settings.storage.path')), true);
    expect($values)->toBe([
        'string' => 'random text',
        'int' => 123,
        'array' => json_encode(['Four']),
        'collection' => json_encode(['Five']),
    ]);
});
