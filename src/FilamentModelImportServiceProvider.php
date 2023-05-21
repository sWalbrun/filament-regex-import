<?php

namespace SWalbrun\FilamentModelImport;

use Exception;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Exceptions\InvalidPackage;
use Spatie\LaravelPackageTools\Package;
use SWalbrun\FilamentModelImport\Commands\MakeImportMapper;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;
use SWalbrun\FilamentModelImport\Import\ModelMapping\MappingRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\RelationRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\Relator;

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
            ->hasTranslations()
            ->hasCommand(MakeImportMapper::class)
            ->hasViews();
    }

    public function boot()
    {
        parent::boot();
        $this->app->singleton(MappingRegistrar::class);
        $this->app->singleton(RelationRegistrar::class);
    }

    /**
     * @throws InvalidPackage
     * @throws Exception
     */
    public function register()
    {
        parent::register();

        /** @var MappingRegistrar $identificationRegistrar */
        $identificationRegistrar = resolve(MappingRegistrar::class);

        /** @var RelationRegistrar $associationRegistrar */
        $associationRegistrar = resolve(RelationRegistrar::class);
        $configIdentifier = static::$name.'.mappers';
        $mappers = collect(config($configIdentifier));
        $mappers->each(function ($class) use ($configIdentifier) {
            if (! (is_subclass_of($class, BaseMapper::class) || is_subclass_of($class, Relator::class))) {
                throw new Exception(
                    'The configured mapper class '.
                    "$class in $configIdentifier does not implement "
                    .BaseMapper::class
                    .' nor '
                    .Relator::class
                );
            }
        })->each(function (string $mapperClass) use ($associationRegistrar, $identificationRegistrar) {
            $mapper = resolve($mapperClass);
            if ($mapper instanceof BaseMapper) {
                $identificationRegistrar->register($mapper);
            }
            if ($mapper instanceof Relator) {
                $associationRegistrar->registerRelator($mapper);
            }
        });

    }
}
