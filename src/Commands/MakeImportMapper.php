<?php

namespace SWalbrun\FilamentModelImport\Commands;

use Filament\Support\Commands\Concerns\CanManipulateFiles;
use Filament\Support\Commands\Concerns\CanValidateInput;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeImportMapper extends Command
{
    use CanManipulateFiles;
    use CanValidateInput;

    private const NAMESPACE = 'Import\\ModelMapping';

    protected $signature = 'filament:make-filament-import-mapper {name} {--F|force}';

    protected $description = 'Creates a new mapper';

    public function handle()
    {
        $modelName = (string) Str::of($this->argument('name'))
            ->trim('/')
            ->trim('\\')
            ->trim(' ')
            ->replace('/', '\\')
            ->replaceLast('Mapper', '');
        $className = Str::of($modelName)->append('Mapper');

        $path = app_path(
            (string) Str::of($className)
                ->prepend(self::NAMESPACE.'\\')
                ->replace('\\', '/')
                ->append('.php'),
        );

        if (! $this->option('force') && $this->checkForCollision([
            $path,
        ])) {
            return static::INVALID;
        }

        $this->copyStubToApp(
            'mapper',
            $path,
            [
                'model' => $modelName,
                'class' => $className,
                'namespace' => self::NAMESPACE,
            ]
        );

        $this->info("Successfully created {$className}!");

        return static::SUCCESS;
    }
}
