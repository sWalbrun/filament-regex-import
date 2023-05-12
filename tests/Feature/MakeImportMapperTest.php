<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

/** @var Filesystem $fileSystem */
$fileSystem = app(Filesystem::class);

afterEach(function () use ($fileSystem) {
    $fileSystem->delete(base_path('app/Import/ModelMapping/UserMapper.php'));
});

it('creates a new mapper', function () use ($fileSystem) {
    $this->artisan('filament:make-filament-import-mapper User')->assertSuccessful();
    $fileSystem->exists(base_path('app/Import/ModelMapping/UserMapper.php'));
    expect($fileSystem->get(base_path('app/Import/ModelMapping/UserMapper.php')))
        ->toEqual($fileSystem->get(__DIR__.'/../__data__/Snapshots/UserMapper.php'));
});

it('creates a new mapper with `mapper` suffix', function () use ($fileSystem) {
    $this->artisan('filament:make-filament-import-mapper UserMapper')->assertSuccessful();
    $fileSystem->exists(base_path('app/Import/ModelMapping/UserMapper.php'));
    expect($fileSystem->get(base_path('app/Import/ModelMapping/UserMapper.php')))
        ->toEqual($fileSystem->get(__DIR__.'/../__data__/Snapshots/UserMapper.php'));
});

it('fails because mapper exists already', function () use ($fileSystem) {
    $fileSystem->put(base_path('app/Import/ModelMapping/UserMapper.php'), '');
    $this->artisan('filament:make-filament-import-mapper UserMapper')->assertExitCode(Command::INVALID);
});
