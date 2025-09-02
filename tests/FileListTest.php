<?php

use Kiwilan\FileList\FileList;

it('can list files', function () {
    $list = FileList::make(PATH_TO_SCAN)->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->not->toBeEmpty();
    expect($list->getFiles())->toHaveCount(8);

    expect($list->getErrors())->toBeNull();

    expect($list->getTimeElapsed())->toBeFloat();
    expect($list->getTimeElapsed())->toBeLessThanOrEqual(1);

    expect($list->getTotal())->toBeInt();
    expect($list->getTotal())->toBe(8);

    expect($list->isSuccess())->toBeTrue();
});

it('can show hidden files', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->showHidden()
        ->skipFilenames(['.DS_Store'])
        ->run();

    expect($list->getFiles())->toHaveCount(9);
});

it('can save as json', function () {
    $path = PATH_TO_OUTPUT.'/files.json';
    FileList::make(PATH_TO_SCAN)
        ->saveAsJson($path)
        ->run();

    expect($path)->toBeFile();

    $contents = file_get_contents($path);
    expect($contents)->toBeString();

    $files = json_decode($contents, true);
    expect($files)->toBeArray();
    expect($files)->toHaveCount(8);
});

it('can handle errors', function () {
    $list = FileList::make('non-existent-path')
        ->safeOnError()
        ->run();

    expect($list->getErrors())->toBeArray();
    expect($list->getErrors())->toHaveCount(1);
    expect($list->getErrors()[0])->toBe('The path `non-existent-path` does not exist.');

    expect(fn () => FileList::make('non-existent-path')->run())->toThrow(Exception::class);
});

it('can limit files', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->limit(3)
        ->run();

    expect($list->getFiles())->toHaveCount(3);
});

it('can skip extensions', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->skipExtensions(['mkv', 'jpg'])
        ->run();

    expect($list->getFiles())->toHaveCount(6);
});

it('can get only extensions', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->onlyExtensions(['mkv', 'jpg'])
        ->run();

    expect($list->getFiles())->toHaveCount(2);
});

it('can use not recursive scan', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->notRecursive()
        ->run();

    expect($list->getFiles())->toHaveCount(5);
});

it('can disable max execution time', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->noMemoryLimit()
        ->run();

    expect($list->getFiles())->toHaveCount(8);
});

it('can list as SplFileInfo', function () {
    $list = FileList::make(PATH_TO_SCAN)->run();

    expect($list->getSplFiles())->toHaveCount(8);
    expect($list->getSplFiles()[0])->toBeInstanceOf(\SplFileInfo::class);
});

it('can list with specific characters', function () {
    $list = FileList::make(PATH_TO_SCAN)->run();
    ray($list)->blue();
    $paths = $list->getFiles();
    $first = $paths[0] ?? null;

    expect($first)->toBeString();
    expect($first)->toMatch('/Ai.no.Kusabi.1992.{tmdb-67325}/');
});
