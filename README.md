# File based configurable settings for laravel applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alisaleem/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/alisaleem/laravel-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/alisaleem/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/alisaleem/laravel-settings)

Store your application settings in any of your applications storage filesystems. The schema for the settings is defined
by a normal PHP class and all primitive types are supported. This also provides IDE type hints when reading to writing
settings.

Updated values are written to the filesystem on destruct.

## Installation

You can install the package via composer:

```bash
composer require alisaleem/laravel-settings
```

Create your own Settings class and extend the abstract Settings class from this package

```php
namespace App;

class MySettings extends \AliSaleem\LaravelSettings\Settings
{
    public string $key;
    public string $anotherKey = 'Default Value';
}
```

Optionally add the helper function. This will also provide IDE type-hinting

```php
if (! function_exists('settings')) {
    function settings(): \App\MySettings
    {
        return resolve(config('settings.class'));
    }
}
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="settings-config"
```

Set the class and storage location in the config file

```php
return [
    'class' => \App\MySettings::class,

    'storage' => [
        'disk' => null,
        'path' => 'settings.json',
    ],
];
```

## Usage

```php
// To retrieve a value
$value = resolve(\App\MySettings::class)->key;
$value = settings()->anotherKey;

// To set a value
resolve(\App\MySettings::class)->key = 'changed';
settings()->anotherKey = 'changed';
```

## Testing

```bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Ali Saleem](https://github.com/alisaleem27)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
