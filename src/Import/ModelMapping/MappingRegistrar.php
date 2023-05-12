<?php

namespace SWalbrun\FilamentModelImport\Import\ModelMapping;

use Illuminate\Support\Collection;

/**
 * Use this register to register your {@link BaseMapper} (one mapping per model).
 */
class MappingRegistrar
{
    private Collection $mappings;

    public function __construct(Collection $mappings)
    {
        $this->mappings = $mappings;
    }

    public function register(BaseMapper $mapping): self
    {
        if (! $this->mappings->contains(fn (BaseMapper $existingMapping) => $existingMapping === $mapping)) {
            $this->mappings->push($mapping);
        }

        return $this;
    }

    /**
     * @return Collection<BaseMapper>
     */
    public function getMappings(): Collection
    {
        return $this->mappings;
    }
}
