<?php

namespace Kiwilan\FileList;

use Kiwilan\FileList\Modules\FileListCommandFind;
use Kiwilan\FileList\Modules\FileListCommandScout;

class FileList
{
    /**
     * @param  string[]  $skipExtensions  The extensions to skip.
     * @param  string[]  $files  The files scanned.
     * @param  string[]  $arguments  The arguments to pass to the command.
     * @param  string[]  $errors  The errors that occurred.
     */
    protected function __construct(
        protected string $pathToScan,
        protected ?string $jsonPath = null,
        protected bool $safeOnError = false,
        protected int|false $limit = false,
        protected bool $recursive = true,
        protected bool $skipHidden = true,
        protected array $onlyExtensions = [],
        protected array $skipExtensions = [],
        protected array $skipFilenames = [],
        protected bool $noMemoryLimit = false,
        protected bool $useNative = true,

        protected array $files = [],
        protected int $total = 0,
        protected bool $success = false,

        protected ?FileListCommand $command = null,

        protected float $timeElapsed = 0,
        protected ?array $errors = null,
    ) {}

    /**
     * Make a new instance of the class.
     *
     * @param  string  $pathToScan  The path to scan.
     */
    public static function make(string $pathToScan): self
    {
        $self = new self($pathToScan);

        return $self;
    }

    /**
     * Set the path to save the json file, if not set, it will not save the json file.
     * If the path does not exist, it will be created.
     */
    public function saveAsJson(string $jsonPath): self
    {
        $this->jsonPath = $jsonPath;

        $jsonDir = dirname($jsonPath);
        if (! file_exists($jsonPath) && ! is_dir($jsonDir)) {
            mkdir($jsonDir, recursive: true);
        }

        return $this;
    }

    /**
     * Safe execution if errors, default will throw an exception.
     */
    public function safeOnError(): self
    {
        $this->safeOnError = true;

        return $this;
    }

    /**
     * Limit the number of files to scan.
     */
    public function limit(int|false $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get only files with these extensions.
     *
     * @param  string[]  $onlyExtensions  The extensions to get, like `['mkv', 'jpg']`.
     */
    public function onlyExtensions(array $onlyExtensions): self
    {

        $this->onlyExtensions = $this->parseExtensions($onlyExtensions);

        return $this;
    }

    /**
     * Skip files with these extensions.
     *
     * @param  string[]  $skipExtensions  The extensions to skip, like `['mkv', 'jpg']`.
     */
    public function skipExtensions(array $skipExtensions): self
    {
        $this->skipExtensions = $this->parseExtensions($skipExtensions);

        return $this;
    }

    /**
     * Skip files with these extensions.
     *
     * @param  string[]  $extensions  The extensions to skip, like `['mkv', 'jpg']`.
     * @return string[]
     */
    private function parseExtensions(array $extensions): array
    {
        // remove the dot from the extensions
        $extensions = array_map(fn ($ext) => str_replace('.', '', $ext), $extensions);
        // trim the extensions
        $extensions = array_map('trim', $extensions);
        // lowercase the extensions
        $extensions = array_map('strtolower', $extensions);

        // remove empty values
        return array_filter($extensions);
    }

    /**
     * Skip files with these filenames.
     *
     * @param  string[]  $skipFilenames  The filenames to skip, like `['.DS_Store', 'Thumbs.db']`.
     */
    public function skipFilenames(array $skipFilenames): self
    {
        $this->skipFilenames = $skipFilenames;

        return $this;
    }

    /**
     * Disable recursive scanning.
     */
    public function notRecursive(): self
    {
        $this->recursive = false;

        return $this;
    }

    /**
     * Show hidden files, default is `false`.
     */
    public function showHidden(): self
    {
        $this->skipHidden = false;

        return $this;
    }

    /**
     * Disable PHP max execution time.
     */
    public function noMemoryLimit(): self
    {
        $this->noMemoryLimit = true;

        return $this;
    }

    /**
     * Scan with `scout`, a Rust CLI to scan files.
     *
     * @link https://github.com/ewilan-riviere/scout
     *
     * Alternative to PHP native scan, override another command.
     */
    public function withScoutSeeker(?string $binaryPath = null): self
    {
        $this->useNative = false;
        $this->command = FileListCommandScout::create([$this->pathToScan, '--print'], $binaryPath);

        return $this;
    }

    /**
     * @deprecated Use `withScoutSeeker` instead.
     */
    public function withScout(?string $binaryPath = null): self
    {
        $this->useNative = false;
        $this->command = FileListCommandScout::create([$this->pathToScan, '--print'], $binaryPath);

        return $this;
    }

    /**
     * Scan with `find`, a bash command to list files.
     *
     * Alternative to PHP native scan, override another command.
     */
    public function withFind(?string $binaryPath = null): self
    {
        $this->useNative = false;
        $this->command = FileListCommandFind::create([$this->pathToScan, '-type', 'f'], $binaryPath);

        return $this;
    }

    public function run(): self
    {
        if (! file_exists($this->pathToScan)) {
            $error = "The path `{$this->pathToScan}` does not exist.";
            if (! $this->safeOnError) {
                throw new \Exception("FileList: {$error}");
            }

            $this->errors[] = $error;

            return $this;
        }

        $startTime = microtime(true);

        if ($this->noMemoryLimit) {
            // disable PHP max execution time
            set_time_limit(0);
        }

        $this->files = $this->execute();
        $this->total = count($this->files);

        $this->cleaning();
        $this->limiting();

        $this->total = count($this->files);
        if ($this->jsonPath) {
            $this->saveJson($this->files);
        }

        if ($this->noMemoryLimit) {
            // reset PHP max execution time
            ini_restore('memory_limit');
        }

        $endTime = microtime(true);

        $this->timeElapsed = $endTime - $startTime;
        $this->timeElapsed = floatval(number_format($this->timeElapsed, 2, '.', ''));

        if (empty($this->errors)) {
            $this->success = true;
        }

        return $this;
    }

    /**
     * Get the files.
     *
     * @return string[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * Get the files as `SplFileInfo` array.
     *
     * @return \SplFileInfo[]
     */
    public function getSplFiles(): array
    {
        $files = [];
        foreach ($this->files as $file) {
            $files[] = new \SplFileInfo($file);
        }

        return $files;
    }

    /**
     * Get the total number of files.
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get the time elapsed.
     */
    public function getTimeElapsed(): float
    {
        return $this->timeElapsed;
    }

    public function getCommand(): ?FileListCommand
    {
        return $this->command;
    }

    /**
     * Get the errors.
     *
     * @return string[]|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get the files.
     *
     * @return string[]
     */
    private function execute(): array
    {
        $files = [];
        if ($this->useNative) {
            $this->native($this->pathToScan, $files);
        } else {
            if ($this->noMemoryLimit) {
                $this->command->noMemoryLimit();
            }

            if ($this->safeOnError) {
                $this->command->safeOnError();
            }

            $this->command->run();
            $files = $this->command->getFiles();
        }

        return $files;
    }

    /**
     * Parse files with DirectoryIterator (robust, no glob quirks).
     *
     * @param  string  $path  The path to scan.
     * @param  string[]  $results  The results.
     */
    private function native(string $path, array &$results = []): array
    {
        $rii = $this->recursive
            ? new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path))
            : new \DirectoryIterator($path);

        /** @var \SplFileInfo $file */
        foreach ($rii as $file) {
            $filename = $file->getFilename();
            if ($filename === '.' || $filename === '..') {
                continue;
            }

            if ($file->isDir() && $this->recursive) {
                // rien à faire : RecursiveIteratorIterator gère déjà la descente
                continue;
            }

            if ($file->isFile()) {
                $results[] = $file->getPathname();
            }
        }

        return $results;
    }

    /**
     * Limit the number of files.
     */
    private function limiting(): void
    {
        if ($this->limit && $this->total > $this->limit) {
            $this->files = array_slice($this->files, 0, $this->limit);
        }
    }

    /**
     * Clean files list.
     */
    private function cleaning(): void
    {
        if (! $this->skipHidden && empty($this->skipExtensions) && empty($this->skipFilenames) && empty($this->onlyExtensions)) {
            return;
        }

        $files = [];
        foreach ($this->files as $file) {
            $filename = explode('/', $file);
            $filename = end($filename);
            if ($this->skipHidden && str_starts_with($filename, '.')) {
                continue;
            }

            $extension = pathinfo($file, PATHINFO_EXTENSION);

            if (! empty($this->onlyExtensions) && ! in_array($extension, $this->onlyExtensions)) {
                continue;
            }

            if (in_array($extension, $this->skipExtensions)) {
                continue;
            }

            if (in_array($filename, $this->skipFilenames)) {
                continue;
            }

            if (empty($filename)) {
                continue;
            }

            $files[] = $file;
        }

        $files = array_filter($files);
        $files = array_values($files);
        sort($files);

        $this->files = $files;
    }

    /**
     * Save files as JSON.
     *
     * @param  string[]  $files
     */
    private function saveJson(array $files): void
    {
        if (file_exists($this->jsonPath)) {
            unlink($this->jsonPath);
        }

        if (! is_dir(dirname($this->jsonPath))) {
            mkdir(dirname($this->jsonPath), recursive: true);
        }

        $contents = json_encode($files, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->jsonPath, $contents);
    }
}
