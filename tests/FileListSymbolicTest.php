<?php

use Kiwilan\FileList\FileList;

it('can list files', function () {
    if (linkinfo(PATH_SYMBOLIC) === -1) {
        symlink(__DIR__.'/data', __DIR__.'/output/data');
    }
    $list = FileList::make(PATH_SYMBOLIC)->run();

    expect($list->getFiles())->toHaveCount(7);
});
