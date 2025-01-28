<?php

namespace SWalbrun\FilamentModelImport\Import;

use SWalbrun\FilamentModelImport\Import\ModelMapping\BaseMapper;

/**
 * A simple data class for scoping {@link BaseMapper} with {@link ColumnMapping::$column model columns} of
 * {@link BaseMapper::$model}.
 */
class ColumnMapping
{
    public function __construct(
        public BaseMapper $mapper,
        public string $column,
        public string $originalRegEx
    ) {}
}
