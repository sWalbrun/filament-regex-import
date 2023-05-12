<?php

namespace SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings;

use Illuminate\Support\Collection;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;

class PostMapper extends BaseMapper
{
    public static bool $hasHookBeenCalled = false;

    public function propertyMapping(): Collection
    {
        return collect(
            [
                'postName' => '/PostName/i',
            ]
        );
    }

    public function uniqueColumns(): array
    {
        return [];
    }

    public function associationHooks(): array
    {
        return [

        ];
    }
}
