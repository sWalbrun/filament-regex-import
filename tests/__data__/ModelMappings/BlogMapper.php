<?php

namespace SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings;

use Illuminate\Support\Collection;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;

class BlogMapper extends BaseMapper
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
