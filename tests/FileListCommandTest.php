<?php

use Kiwilan\FileList\FileList;

it('can list files with scout', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->withScout()
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);

    expect(fn () => FileList::make('non-existent-path')
        ->withScout()
        ->throwOnError()
        ->run()
    )->toThrow(Exception::class);
})->skip(PHP_OS_FAMILY === 'Windows');

it('can list files with scout and print command', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->withScout()
        ->run();

    $currentUser = exec('whoami');
    $command = $list->getCommand();
    expect($command->getName())->toBe('scout');
    expect($command->getCommand())->toBeString();
    expect($command->getCommand())->toContain('scout');
    expect($command->getArguments())->toBeArray();
    expect($command->getErrors())->toBeNull();
    expect($command->getFiles())->toBeArray();
    expect($command->getOutput())->toBeString();
    expect($command->getOutputArray())->toBeArray();
    expect($command->getUser())->toBe($currentUser);
    expect($command->isSuccess())->toBeTrue();
    expect($command->toArray())->toBeArray();
})->skip(PHP_OS_FAMILY === 'Windows');

it('can list files with scout with path', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->withScout('/usr/local/bin/scout')
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);
})->skip(PHP_OS_FAMILY === 'Windows');

it('can list files with find', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->noMemoryLimit()
        ->withFind()
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);
})->skip(PHP_OS_FAMILY === 'Windows');
