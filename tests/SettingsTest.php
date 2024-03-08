<?php

use AliSaleem\LaravelSettings\Support\Field;
use AliSaleem\LaravelSettings\Support\Type;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake();

    config()->set('settings.schema', [
        new Field('text', Type::Text),
        new Field('integer', Type::Integer),
    ]);
});

it('can read config from storage', function () {
    $values = [
        'text' => 'hello',
        'integer' => 123,
    ];
    $this->storage()->put(config('settings.storage.path'), json_encode($values));

    expect(settings()->text)->toBe($values['text']);
    expect(settings()->integer)->toBe($values['integer']);
});

it('can write config to storage', function () {
    settings()->text = 'hello';
    settings()->integer = 123;

    $values = json_decode($this->storage()->get(config('settings.storage.path')), true);
    expect($values)->toBe([
        'text' => 'hello',
        'integer' => 123,
    ]);
});

it('correctly casts CSV fields', function () {
    config()->set('settings.schema', [
        new Field('csv', Type::CSV),
    ]);

    $csvArray = ['One', 'Two', 'Three', 'F,our'];
    settings()->csv = $csvArray;
    expect(settings()->csv)->toBe($csvArray);

    $emptyCSV = [];
    settings()->csv = $emptyCSV;
    expect(settings()->csv)->toBe($emptyCSV);

    settings()->csv = null;
    expect(settings()->csv)->toBe($emptyCSV);

    settings()->csv = '';
    expect(settings()->csv)->toBe($emptyCSV);
});

it('throws exception on invalid keys', function () {
    new Field('Invalid Key');
})->expectException(InvalidArgumentException::class);

it('removes storage file if there are no settings', function () {
    settings()->text = 'hello';
    unset(settings()->text);

    expect($this->storage()->exists(config('settings.storage.path')))->toBeFalse();
});
