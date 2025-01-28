<?php

namespace SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;
use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;
use SWalbrun\FilamentModelImport\Import\ModelMapping\Relator;
use SWalbrun\FilamentModelImport\Tests\__Data__\Models\User;

class UserMapper extends BaseMapper implements Relator
{
    public function __construct()
    {
        parent::__construct(new User);
    }

    public function uniqueColumns(): array
    {
        return [
            User::COL_EMAIL,
        ];
    }

    public function propertyMapping(): Collection
    {
        return collect([
            User::COL_NAME => '/Benutzername/i',
            User::COL_EMAIL => '/E-Mail/i',
            User::COL_JOIN_DATE => '/Beitrittsdatum/i',
            User::COL_CONTRIBUTION_GROUP => '/Beitragsgruppe/i',
            User::COL_COUNT_SHARES => '/Anzahl d(.|er) Anteile/i',
            User::COL_CREATED_AT => '/Angelegt am/i',
        ]);
    }

    public function relatingClosures(): Collection
    {
        return collect([
            fn (User $user, Role $role) => $user->roles()->saveMany([$role]),
        ]);
    }
}
