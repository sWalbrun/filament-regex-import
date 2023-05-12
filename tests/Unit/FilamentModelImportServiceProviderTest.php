<?php

use SWalbrun\FilamentModelImport\FilamentModelImportServiceProvider;
use SWalbrun\FilamentModelImport\Import\ModelMapping\MappingRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\RelationRegistrar;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\UserMapper;

beforeEach(function () {
    config()->set('filament-model-import.mappers', [
        UserMapper::class,
    ]);
});
it('successfully registers the configured mappers', function () {

    /** @var FilamentModelImportServiceProvider $provider */
    $provider = resolve(FilamentModelImportServiceProvider::class, ['app' => app()]);
    $provider->register();

    /** @var MappingRegistrar $mappingRegistrar */
    $mappingRegistrar = resolve(MappingRegistrar::class);
    expect(get_class($mappingRegistrar->getMappings()->first()))->toBe(UserMapper::class);
});

it('successfully registers the configured relator', function () {
    /** @var FilamentModelImportServiceProvider $provider */
    $provider = resolve(FilamentModelImportServiceProvider::class, ['app' => app()]);
    $provider->register();

    /** @var RelationRegistrar $relationRegistrar */
    $relationRegistrar = resolve(RelationRegistrar::class);

    expect($relationRegistrar->getClosures())->toEqual((new UserMapper())->relatingClosures());
});

it('fails for a wrong configuration', function () {
    config()->set('filament-model-import.mappers', [
        stdClass::class,
    ]);

    /** @var FilamentModelImportServiceProvider $provider */
    $provider = resolve(FilamentModelImportServiceProvider::class, ['app' => app()]);
    expect(fn () => $provider->register())->toThrow(Exception::class);
});
