# Filament Plugin for importing CSV and XLS files as models

[![Latest Version on Packagist](https://img.shields.io/packagist/v/swalbrun/filament-model-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-model-import)
![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/swalbrun/filament-model-import/run-tests.yml?branch=main)
[![Total Downloads](https://img.shields.io/packagist/dt/swalbrun/filament-model-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-model-import)

This Filament Plugin will enable you to import files to upsert models by matching columns via regex.

## Installation

You can install the package via composer:

```bash
composer require swalbrun/filament-model-import
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-model-import-config"
```

This is the contents of the published config file:

```php
return [
    'accepted_mimes' => [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv',
        'text/plain',
        'csv',
    ],
    'mappers' => [
    ]
];
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
