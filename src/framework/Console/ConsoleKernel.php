<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Console;

use Closure;
use Baueri\Spire\Framework\Application;
use Baueri\Spire\Framework\Console\BaseCommands\ListCommands;
use Baueri\Spire\Framework\Console\Exception\CommandNotFoundException;
use Throwable;

class ConsoleKernel
{
    /**
     * @var Command[]|string[]
     */
    private array $commands;

    public function __construct(
        protected readonly Application $app
    ) {
        $this->loadCommands(__DIR__ . '/BaseCommands');
    }

    public function loadCommands(string $path): static
    {
        $files = rglob(ltrim($path, '/') . '/*.php');

        $commands = [];
        foreach ($files as $file) {
            $file = str_replace($this->app->root(), '', $file);
            $command = '\\' . mb_ucfirst(str_replace(['/', '.php'], ['\\', ''], $file));

            if (class_exists($command) && is_subclass_of($command, Command::class)) {
                $commands[] = $command;
            }
        }

        $this->withCommand($commands);

        return $this;
    }

    /**
     * @throws CommandNotFoundException
     */
    public function handle()
    {
        $args = $this->getArgs();

        $signature = array_shift($args);
        $command = $this->getCommand($signature);

        try {
            if (is_callable($command)) {
                return $command(...$this->app->getDependencies($command)) ?? Command::SUCCESS;
            }

            $command->withArgs($args);
            return $command->handle() ?? Command::SUCCESS;
        } catch (Throwable $e) {
            app()->handleError($e);
            return Command::FAILURE;
        }
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * @template T of Command
     * @param class-string<T>[]|class-string<T> $command
     * @param $handler
     * @return $this
     */
    public function withCommand(array|string $command, $handler = null): static
    {
        if (is_array($command)) {
            foreach ($command as $singleCommand) {
                $this->commands[$singleCommand::signature()] = $singleCommand;
            }
        } elseif ($handler) {
            $this->commands[$command] = $handler;
        } else {
            $this->commands[$command::signature()] = $command;
        }

        return $this;
    }

    /**ar
     * @throws CommandNotFoundException
     */
    public function getCommand(?string $signature): Command|Closure
    {
        if (!$signature) {
            return $this->app->make(ListCommands::class);
        }

        foreach ($this->getCommands() as $registeredSignature => $command) {
            if ($registeredSignature == $signature) {
                return $command instanceof Closure ? $command : $this->app->get($command);
            }
        }

        throw new CommandNotFoundException("command not found: $signature");
    }

    public function getArgs()
    {
        global $argv;

        $args = $argv;

        array_shift($args);

        return $args;
    }
}
