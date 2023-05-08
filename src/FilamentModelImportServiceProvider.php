<?php

namespace SWalbrun\FilamentModelImport;

use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;
use SWalbrun\FilamentModelImport\Import\ModelMapping\AssociationRegister;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationRegister;

class FilamentModelImportServiceProvider extends PluginServiceProvider
{
    public static string $name = 'filament-model-import';

    protected array $pages = [
        ImportPage::class,
    ];

    protected array $styles = [
        'plugin-filament-model-import' => __DIR__ . '/../resources/dist/filament-model-import.css',
    ];

    protected array $scripts = [
        'plugin-filament-model-import' => __DIR__ . '/../resources/dist/filament-model-import.js',
    ];

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)->hasViews();
    }

    public function boot()
    {
        parent::boot();
        $this->app->singleton(IdentificationRegister::class);
        $this->app->singleton(AssociationRegister::class);
    }
}
