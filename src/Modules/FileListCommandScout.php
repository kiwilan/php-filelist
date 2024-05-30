<?php

namespace Kiwilan\FileList\Modules;

use Kiwilan\FileList\FileListCommand;

class FileListCommandScout extends FileListCommand
{
    public static function create(array $arguments, ?string $binaryPath = null): self
    {
        $self = new self($binaryPath ?: 'scout');
        $self->arguments = $arguments;

        return $self;
    }

    public function getFiles(): array
    {
        $files = [];

        $lastLine = end($this->outputArray);
        $path = explode(':', $lastLine)[1];
        $path = trim($path);

        if (! file_exists($path)) {
            return $files;
        }

        $contents = file_get_contents($path);
        $json = json_decode($contents, true);

        if (! array_key_exists('files', $json)) {
            return $files;
        }

        $files = $json['files'];
        unlink($path);

        return $files;
    }
}
