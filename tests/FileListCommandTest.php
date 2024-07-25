<?php

use Kiwilan\FileList\FileList;

it('can list files with scout', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->withScoutSeeker()
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);

    expect(fn () => FileList::make('non-existent-path')
        ->withScoutSeeker()
        ->run()
    )->toThrow(Exception::class);
});

it('can list files with scout and print command', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->withScoutSeeker()
        ->run();

    $currentUser = exec('whoami');
    $command = $list->getCommand();
    expect($command->getName())->toBe('scout-seeker');
    expect($command->getCommand())->toBeString();
    expect($command->getCommand())->toContain('scout-seeker');
    expect($command->getArguments())->toBeArray();
    expect($command->getErrors())->toBeNull();
    expect($command->getFiles())->toBeArray();
    expect($command->getOutput())->toBeString();
    expect($command->getOutputArray())->toBeArray();
    expect($command->getUser())->toContain($currentUser);
    expect($command->isSuccess())->toBeTrue();
    expect($command->toArray())->toBeArray();
});

it('can list files with scout with path', function () {
    $which = exec('which scout-seeker');
    $list = FileList::make(PATH_TO_SCAN)
        ->withScoutSeeker($which)
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);
});

it('can list files with find', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->noMemoryLimit()
        ->withFind()
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);
});

it('can throw error if command not found', function () {
    expect(fn () => FileList::make(PATH_TO_SCAN)
        ->withScoutSeeker('non-existent-command')
        ->run())->toThrow(Exception::class);
});

it('can safe error if command not found', function () {
    expect(fn () => FileList::make(PATH_TO_SCAN)
        ->withScoutSeeker('non-existent-command')
        ->safeOnError()
        ->run())->not->toThrow(Exception::class);
});
