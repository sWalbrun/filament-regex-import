<?php

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

/** @var Filesystem $fileSystem */
$fileSystem = app(Filesystem::class);
$path = 'app'.DIRECTORY_SEPARATOR.'Import'.DIRECTORY_SEPARATOR.'ModelMapping'.DIRECTORY_SEPARATOR.'UserMapper.php';

beforeEach(function () use ($path, $fileSystem) {
    $userMapperPath = base_path($path);
    if ($fileSystem->exists($userMapperPath)) {
        $fileSystem->delete($userMapperPath);
    }
});

afterEach(function () use ($path, $fileSystem) {
    $userMapperPath = base_path($path);
    if ($fileSystem->exists($userMapperPath)) {
        $fileSystem->delete($userMapperPath);
    }
});

it('creates a new mapper', function () use ($path, $fileSystem) {
    $this->artisan('filament:make-filament-import-mapper User')->assertSuccessful();
    $fileSystem->exists(base_path($path));
    expect($fileSystem->get(base_path($path)))
        ->toEqual($fileSystem->get(__DIR__
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'__data__'
            .DIRECTORY_SEPARATOR
            .'Snapshots'
            .DIRECTORY_SEPARATOR
            .'UserMapper.php'));
});

it('creates a new mapper with `mapper` suffix', function () use ($path, $fileSystem) {
    $this->artisan('filament:make-filament-import-mapper UserMapper')->assertSuccessful();
    expect($fileSystem->exists(base_path($path)))->toBeTrue();
    expect($fileSystem->get(base_path($path)))
        ->toEqual($fileSystem->get(__DIR__
            .DIRECTORY_SEPARATOR
            .'..'
            .DIRECTORY_SEPARATOR
            .'__data__'
            .DIRECTORY_SEPARATOR
            .'Snapshots'
            .DIRECTORY_SEPARATOR
            .'UserMapper.php'));
});

it('fails because mapper exists already', function () use ($path, $fileSystem) {
    $fileSystem->ensureDirectoryExists(
        (string) Str::of(base_path($path))
            ->beforeLast(DIRECTORY_SEPARATOR),
    );
    $fileSystem->put(base_path($path), '');
    $this->artisan('filament:make-filament-import-mapper UserMapper')->assertExitCode(Command::INVALID);
});
