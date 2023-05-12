<?php

namespace SWalbrun\FilamentModelImport;

use Exception;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;
use SWalbrun\FilamentModelImport\Import\ModelMapping\AssociationOf;
use SWalbrun\FilamentModelImport\Import\ModelMapping\AssociationRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationOf;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationRegistrar;

class FilamentModelImportServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-model-import';

    protected array $pages = [
        ImportPage::class,
    ];

    protected array $styles = [
        'plugin-filament-model-import' => __DIR__.'/../resources/dist/filament-model-import.css',
    ];

    protected array $scripts = [
        'plugin-filament-model-import' => __DIR__.'/../resources/dist/filament-model-import.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasConfigFile()
            ->hasViews();
    }

    public function boot()
    {
        parent::boot();
        $this->app->singleton(IdentificationRegistrar::class);
        $this->app->singleton(AssociationRegistrar::class);
    }

    /**
     * @throws InvalidPackage
     * @throws Exception
     */
    public function register()
    {
        parent::register();

        /** @var IdentificationRegistrar $identificationRegistrar */
        $identificationRegistrar = resolve(IdentificationRegistrar::class);

        /** @var AssociationRegistrar $associationRegistrar */
        $associationRegistrar = resolve(AssociationRegistrar::class);
        $configIdentifier = static::$name.'.mappers';
        $mappers = collect(config($configIdentifier));
        $mappers->each(function ($class) use ($configIdentifier) {
            if (! (is_subclass_of($class, IdentificationOf::class) || is_subclass_of($class, AssociationOf::class))) {
                throw new Exception(
                    'The configured mapper class '.
                    "$class in $configIdentifier does not implement "
                    .IdentificationOf::class
                    .' nor '
                    .AssociationOf::class
                );
            }
        })->each(function (string $mapperClass) use ($associationRegistrar, $identificationRegistrar) {
            $mapper = resolve($mapperClass);
            if ($mapper instanceof IdentificationOf) {
                $identificationRegistrar->register($mapper);
            }
            if ($mapper instanceof AssociationOf) {
                $associationRegistrar->registerAssociationOf($mapper);
            }
        });

    }
}
