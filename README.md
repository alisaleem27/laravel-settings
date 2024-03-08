# File based configurable settings for laravel applications

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alisaleem/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/alisaleem/laravel-settings)
[![Total Downloads](https://img.shields.io/packagist/dt/alisaleem/laravel-settings.svg?style=flat-square)](https://packagist.org/packages/alisaleem/laravel-settings)

This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require alisaleem/laravel-settings
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-settings-config"
```

This is the contents of the published config file:

```php
return [
    'provider' => \App\Support\Settings::class,
    'storage'  => storage_path('app/settings.json'),
];
```

## Usage

```php
$laravelSettings = new AliSaleem\LaravelSettings();
echo $laravelSettings->echoPhrase('Hello, AliSaleem!');
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
