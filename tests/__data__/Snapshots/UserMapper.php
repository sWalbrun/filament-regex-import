<?php

namespace App\Import\ModelMapping;

use Illuminate\Support\Collection;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;
use SWalbrun\FilamentModelImport\Import\ModelMapping\Relator;

class UserMapper extends BaseMapper implements Relator
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function uniqueColumns(): array
    {
        return [
            'column',
        ];
    }

    public function propertyMapping(): Collection
    {
        return collect([
            'column' => '/regex/i',
        ]);
    }

    public function relatingClosures(): Collection
    {
        return collect([
            // fn (User $model, AnotherModel $anotherModel) => $model->anotherModel()->saveMany([$anotherMode]),
        ]);
    }
}
