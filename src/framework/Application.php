<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework;

use Baueri\Spire\Framework\Enums\Environment;
use Baueri\Spire\Framework\Support\Config;
use Baueri\Spire\Framework\Traits\DispatchesEvents;
use Throwable;

class Application extends Container
{
    use DispatchesEvents;

    /**
     * @var Bootstrapper[]
     */
    protected array $bootstrap = [];

    protected Config $config;

    private string $locale;

    protected function __construct(public readonly string $root)
    {
        $this->locale = 'hu';
    }

    public static function create(string $root): static
    {
        $instance = new static($root);
        self::setInstance($instance);
        return $instance;
    }

    public function boot(): void
    {
        $this->runEvent('booting');
        foreach ($this->bootstrap as $bootstrapper) {
            $this->resolve($bootstrapper, 'boot');
        }
        $this->runEvent('booted');
    }

    public function config(string $key = null, $default = null): mixed
    {
        $this->config ??= $this->get(Config::class);
        if (!$key) {
            return $this->config;
        }

        return $this->config->get($key, $default);
    }

    public function handleError(Throwable $e): void
    {
        $errorHandler = $this->get('errorHandler');

        if (is_callable($errorHandler)) {
            $errorHandler($e);
            return;
        }

        $errorHandler->handle($e);
    }

    /**
     * @phpstan-param class-string<Bootstrapper>|Bootstrapper|callable|array $bootstrapper
     */
    public function bootWith(array|string|callable|Bootstrapper $bootstrapper): void
    {
        if (is_array($bootstrapper)) {
            array_walk($bootstrapper, fn ($boot) => $this->bootWith($boot));
            return;
        }

        if (is_callable($bootstrapper)) {
            $this->bootstrap[] = $bootstrapper;
            return;
        }

        $this->bootstrap[] = $bootstrapper instanceof Bootstrapper ? $bootstrapper : $this->make($bootstrapper);
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setLocale($lang): void
    {
        $this->locale = $lang;
    }

    public function envIs(Environment $env): bool
    {
        return $this->getEnvironment() === $env->name;
    }

    public function getEnvironment(): string
    {
        return $this->config('app.environment');
    }

    public function isTest(): bool
    {
        return $this->envIs(Environment::test);
    }

    public function debug(): bool
    {
        return $this->config('app.debug') && !$this->envIs(Environment::production);
    }

    public function root(string $path = ''): string
    {
        return $this->root . ltrim($path, DIRECTORY_SEPARATOR);
    }

    public function pub_path(string $path): string
    {
        return $this->root('public' . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR));
    }

    public function __destruct()
    {
        $this->runEvent('terminated');
    }
}
