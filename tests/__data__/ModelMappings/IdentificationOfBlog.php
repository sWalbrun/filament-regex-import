<?php

namespace SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings;

use Illuminate\Support\Collection;
use SWalbrun\FilamentModelImport\Import\ModelMapping\IdentificationOf;

class IdentificationOfBlog extends IdentificationOf
{
    public function propertyMapping(): Collection
    {
        return collect([
            'blogName' => '/BlogName/i',
        ]);
    }

    public function uniqueColumns(): array
    {
        return [];
    }
}
