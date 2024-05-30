<?php

namespace Kiwilan\FileList\Modules;

use Kiwilan\FileList\FileListCommand;

class FileListCommandFind extends FileListCommand
{
    public static function create(array $arguments, ?string $binaryPath = null): self
    {
        // Get-ChildItem -Path "C:\path\to\directory" -Recurse -File
        $self = new self($binaryPath ? 'find' : null);
        $self->arguments = $arguments;

        return $self;
    }

    public function getFiles(): array
    {
        return $this->outputArray;
    }
}
