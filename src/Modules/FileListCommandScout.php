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
        $files = $this->parseOutput();

        return $files;
    }

    private function parseOutput(): array
    {
        $files = $this->outputArray;

        return $files;
    }

    private function parseJsonOutput(): array
    {
        $files = [];
        if (! $this->available) {
            return $files;
        }

        $path = null;
        foreach ($this->outputArray as $line) {
            if (str_contains($line, 'Output file:')) {
                $path = explode(':', $line)[1];
                $path = trim($path);
                break;
            }
        }

        if ($path === null) {
            $this->errors[] = 'No output file found.';
            $this->success = false;

            return $files;
        }

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
