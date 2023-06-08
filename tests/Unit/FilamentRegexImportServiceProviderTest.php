<?php

use SWalbrun\FilamentModelImport\FilamentRegexImportServiceProvider;
use SWalbrun\FilamentModelImport\Import\ModelMapping\MappingRegistrar;
use SWalbrun\FilamentModelImport\Import\ModelMapping\RelationRegistrar;
use SWalbrun\FilamentModelImport\Tests\__Data__\ModelMappings\UserMapper;

beforeEach(function () {
    config()->set('filament-regex-import.mappers', [
        UserMapper::class,
    ]);
});
it('successfully registers the configured mappers', function () {

    /** @var FilamentRegexImportServiceProvider $provider */
    $provider = resolve(FilamentRegexImportServiceProvider::class, ['app' => app()]);
    $provider->register();
    $provider->boot();

    /** @var MappingRegistrar $mappingRegistrar */
    $mappingRegistrar = resolve(MappingRegistrar::class);
    expect(get_class($mappingRegistrar->getMappings()->first()))->toBe(UserMapper::class);
});

it('successfully registers the configured relator', function () {
    /** @var FilamentRegexImportServiceProvider $provider */
    $provider = resolve(FilamentRegexImportServiceProvider::class, ['app' => app()]);
    $provider->register();
    $provider->boot();

    /** @var RelationRegistrar $relationRegistrar */
    $relationRegistrar = resolve(RelationRegistrar::class);

    expect($relationRegistrar->getClosures())->toEqual((new UserMapper())->relatingClosures());
});

it('fails for a wrong configuration', function () {
    config()->set('filament-regex-import.mappers', [
        stdClass::class,
    ]);

    /** @var FilamentRegexImportServiceProvider $provider */
    $provider = resolve(FilamentRegexImportServiceProvider::class, ['app' => app()]);
    expect(function () use ($provider) {
        $provider->register();
        $provider->boot();
    })->toThrow(Exception::class);
});
