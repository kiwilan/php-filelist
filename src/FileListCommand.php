<?php

namespace Kiwilan\FileList;

abstract class FileListCommand
{
    protected array $arguments = [];

    protected ?string $command = null;

    protected ?string $user = null;

    protected ?string $output = null;

    /**
     * @var string[]|null The output as an array.
     */
    protected ?array $outputArray = null;

    protected bool $success = false;

    protected bool $noMemoryLimit = false;

    protected bool $throwOnError = false;

    protected bool $available = true;

    /**
     * @var string[]|null The errors that occurred.
     */
    protected ?array $errors = null;

    protected function __construct(
        protected string $name,
    ) {
    }

    /**
     * Make a new instance of the class.
     *
     * @param  string  $command  The command to use.
     * @param  string[]  $arguments  The arguments to pass to the command.
     */
    public static function make(string $command, array $arguments): self
    {
        $self = new self($command, $arguments);

        return $self;
    }

    /**
     * Create new instance of module command.
     *
     * @param  string[]  $arguments  The arguments to pass to the command.
     */
    abstract public static function create(array $arguments, ?string $binaryPath = null): self;

    /**
     * Get files from the output.
     *
     * @return string[] The files.
     */
    abstract public function getFiles(): array;

    public function noMemoryLimit(): self
    {
        $this->noMemoryLimit = true;

        return $this;
    }

    public function throwOnError(): self
    {
        $this->throwOnError = true;

        return $this;
    }

    /**
     * Get the name of the command.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get full command with arguments.
     */
    public function getCommand(): ?string
    {
        return $this->command;
    }

    /**
     * Get the user that ran the command.
     */
    public function getUser(): ?string
    {
        return $this->user;
    }

    /**
     * Get the output of the command.
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * Get the output as an array.
     */
    public function getOutputArray(): ?array
    {
        return $this->outputArray;
    }

    /**
     * Get the success status of the command.
     */
    public function isSuccess(): bool
    {
        return $this->success;
    }

    /**
     * Get the availability of the command.
     */
    public function isAvailable(): bool
    {
        return $this->available;
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

    /**
     * Run the command.
     */
    public function run(): self
    {
        if (! $this->commandExists($this->name)) {
            $error = 'Command not found: '.$this->name;
            $this->errors[] = $error;

            if ($this->throwOnError) {
                throw new \Exception("FileList: {$error}");
            }

            $this->available = false;

            return $this;
        }

        $this->command = $this->name.' '.implode(' ', $this->arguments);

        $output = null;
        $success = false;

        if ($this->noMemoryLimit) {
            set_time_limit(0);
        }

        try {
            exec($this->command, $output, $success);
        } catch (\Throwable $th) {
            $this->errors[] = $th->getMessage();

            if ($this->throwOnError) {
                throw new \Exception("FileList: {$th->getMessage()}");
            }
        }

        $this->user = get_current_user();
        $this->output = implode(PHP_EOL, $output);
        $this->outputArray = $output;
        $this->success = true;

        return $this;
    }

    private function commandExists(string $cmd): bool
    {
        $return = shell_exec(sprintf('which %s', escapeshellarg($cmd)));

        return ! empty($return);
    }
}
