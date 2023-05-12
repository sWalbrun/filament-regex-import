<?php

namespace SWalbrun\FilamentModelImport\Import\ModelMapping;

use Illuminate\Support\Collection;

interface Relator
{
    public function relatingClosures(): Collection;
}
