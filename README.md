# Filament Plugin for importing CSV and XLS files via regex

[![Latest Version on Packagist](https://img.shields.io/packagist/v/swalbrun/filament-regex-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-regex-import)
![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/swalbrun/filament-regex-import/run-tests.yml?branch=main)
<a href="https://codecov.io/gh/sWalbrun/filament-regex-import" >
<img src="https://codecov.io/gh/sWalbrun/filament-regex-import/branch/main/graph/badge.svg?token=9HG0Q05UW2"/>
</a>
[![Total Downloads](https://img.shields.io/packagist/dt/swalbrun/filament-regex-import.svg?style=flat-square)](https://packagist.org/packages/swalbrun/filament-regex-import)

This Filament Plugin will enable you to import files to upsert models by matching columns via regex.

## Installation

You can install the package via composer:

```bash
composer require swalbrun/filament-regex-import
```

Create a mapper using the make command

```bash
php artisan filament:make-filament-import-mapper UserMapper
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-regex-import-config"
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
    ],
    'navigation_group' => 'Import',
];
```

You can publish the translation file with:

```bash
php artisan vendor:publish --tag="filament-regex-import-translations"
```

## Features

### Matching header columns with configured regex

Matches given mappers' regex with model columns

| Username  | EMail                           |
|-----------|---------------------------------|
| Sebastian | sebastian@walbrun-consulting.de |
| John      | john@doe.test                   |

```php
 public function propertyMapping(): Collection
    {
        return collect([
            'name' => '/(user|first|last)?name)/i',
            'email' => '/(E(-|_)?)?Mail/i',
        ]);
    }
```

### Detecting overlapping regexes

Fails in case two regexes are matching the same column.

### Upserting models via unique keys

Creates or updates models taking care of the given unique columns

```php
public function uniqueColumns(): array
    {
        return [
            'email',
        ];
    }
```

### Relating models

Call hooks for relating found models. The hooks will get called in case **all** hooks arguments models have been found

```php
public function relatingClosures(): Collection
    {
        return collect([
            fn (User $user, Role $role) => $user->roles()->saveMany([$role]),
            fn (User $user) => event(new UserImported($user)),
            // Only gets called if a user, role and post with the matching type has been found by import
            function (User $user, Role $role, Post $post)  {
                if ($role->is('user')) {
                    $user->post()->associate($post)->save();
                }
            };
        ]);
    }
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
