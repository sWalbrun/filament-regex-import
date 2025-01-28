<?php

namespace SWalbrun\FilamentModelImport;

use Filament\Contracts\Plugin;
use Filament\Panel;
use SWalbrun\FilamentModelImport\Filament\Pages\ImportPage;

class FilamentRegexImportPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return FilamentRegexImportServiceProvider::$name;
    }

    public function register(Panel $panel): void
    {
        $panel->pages([
            ImportPage::class,
        ]);
    }

    public function boot(Panel $panel): void {}
}
