# This is my package filament-model-import

[![Latest Version on Packagist](https://img.shields.io/packagist/v/swalbrun/filament-model-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-model-import)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/swalbrun/filament-model-import/run-tests?label=tests)](https://github.com/swalbrun/filament-model-import/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/swalbrun/filament-model-import/Check%20&%20fix%20styling?label=code%20style)](https://github.com/swalbrun/filament-model-import/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/swalbrun/filament-model-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-model-import)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require swalbrun/filament-model-import
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-model-import-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-model-import-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-model-import-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$filament-model-import = new SWalbrun\FilamentModelImport();
echo $filament-model-import->echoPhrase('Hello, SWalbrun!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sebastian Walbrun](https://github.com/sWalbrun)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
