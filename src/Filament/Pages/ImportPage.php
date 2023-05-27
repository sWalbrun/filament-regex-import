<?php

namespace SWalbrun\FilamentModelImport\Filament\Pages;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use SWalbrun\FilamentModelImport\Import\ColumnMapping;
use SWalbrun\FilamentModelImport\Import\Services\ImportService;

class ImportPage extends Page
{
    use WithFileUploads;
    use InteractsWithForms;

    /**
     * Needed for the file upload input to work properly.
     *
     * @var mixed
     */
    public $import = null;

    private ImportService $importService;

    public const IMPORT = 'import';

    protected static ?string $navigationIcon = 'bi-filetype-xlsx';

    protected static string $view = 'filament-regex-import::pages.import';

    protected static string $viewIdentifier = 'import';

    protected static function getNavigationGroup(): ?string
    {
        return config('filament-regex-import.navigation_group');
    }

    public function __construct($id = null)
    {
        parent::__construct($id);
        $this->importService = resolve(ImportService::class);
    }

    protected static function getNavigationLabel(): string
    {
        return trans('filament-regex-import::filament-regex-import.resource.navigation.label');
    }

    protected function getTitle(): string
    {
        return trans('filament-regex-import::filament-regex-import.resource.title');
    }

    protected function getActions(): array
    {
        return [
            Action::make('save')
                ->label(trans('Import'))
                ->requiresConfirmation()
                ->form([
                    KeyValue::make('Mapping')
                        ->disableEditingValues()
                        ->disableEditingKeys()
                        ->disableAddingRows()
                        ->disableDeletingRows()
                        ->afterStateHydrated(fn ($component) => $component->state($this->getColumnMapping())),
                ])
                ->modalContent()
                ->modalWidth(400)
                ->action(function () {
                    $this->import();
                })
                ->disabled(fn () => ! isset($this->import)),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            FileUpload::make(self::IMPORT)
                ->label(trans('Import'))
                ->acceptedFileTypes(config('filament-regex-import.accepted_mimes'))
                ->imagePreviewHeight(500),
        ];
    }

    private function getColumnMapping(): Collection
    {
        try {
            DB::beginTransaction();
            $this->import();

            return $this->importService->getHeadingToColumnMapping()
                ->mapWithKeys(fn (ColumnMapping $mapping) => [
                    $mapping->originalRegEx => trans(class_basename($mapping->mapper->model::class))
                        .': '
                        .trans($mapping->column),
                ]);
        } finally {
            DB::rollBack();
        }
    }

    private function import(): void
    {
        Excel::import(
            $this->importService,
            reset($this->import)->getRealPath()
        );
    }
}
