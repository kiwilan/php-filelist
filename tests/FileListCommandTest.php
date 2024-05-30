<?php

use Kiwilan\FileList\FileList;

it('can list files with scout', function () {
    // $list = FileList::make(PATH_TO_SCAN)
    //     ->withScout()
    //     ->run();

    // expect($list->getFiles())->toBeArray();
    // expect($list->getFiles())->toHaveCount(7);

    $list = FileList::make(PATH_TO_SCAN)
        ->withScout('/usr/local/bin/scout')
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);

    expect(fn () => FileList::make('non-existent-path')
        ->withScout()
        ->throwOnError()
        ->run()
    )->toThrow(Exception::class);
});

it('can list files with find', function () {
    $list = FileList::make(PATH_TO_SCAN)
        ->noMemoryLimit()
        ->withFind()
        ->run();

    expect($list->getFiles())->toBeArray();
    expect($list->getFiles())->toHaveCount(7);
});
