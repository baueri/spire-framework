<?php

namespace Baueri\Spire\Framework;

use Baueri\Spire\Framework\Container\Container;
use Baueri\Spire\Framework\Database\BootListeners;
use Baueri\Spire\Framework\Database\QueryLog;
use Baueri\Spire\Framework\Enums\Environment;
use Baueri\Spire\Framework\Http\View\Bootstrappers\BootDirectives;
use Baueri\Spire\Framework\Support\Config;
use Baueri\Spire\Framework\Traits\DispatchesEvents;
use Exception;
use Throwable;

class Application extends Container
{
    use DispatchesEvents;

    protected static Application $singleton;

    /**
     * @var array<class-string<Bootstrapper>>
     */
    protected array $bootstrappers = [
        BootDirectives::class,
        BootListeners::class,
    ];

    private string $locale;

    /**
     * @throws Exception
     */
    public function __construct(public readonly string $root)
    {
        $this->locale = 'hu';
        $this->singleton(static::class, function () {
            return static::getInstance();
        });

        $this->singleton(QueryLog::class);
        static::$singleton = $this;
    }

    public static function getInstance(): Application
    {
        return static::$singleton;
    }

    public function boot(): void
    {
        $this->runEvent('booting');
        foreach ($this->bootstrappers as $bootstrapper) {
            if (!is_callable($bootstrapper)) {
                $this->make($bootstrapper)->boot();
            } else {
                $this->resolve($bootstrapper);
            }
        }
        $this->runEvent('booted');
    }

    public function config(string $key = null, $default = null): mixed
    {
        if (!$key) {
            return $this->get(Config::class);
        }

        return $this->get(Config::class)->get($key, $default);
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
     * @phpstan-param class-string<Bootstrapper> $bootstrapper
     */
    public function bootWith($bootstrapper): void
    {
        $this->bootstrappers[] = $bootstrapper;
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
        return config('app.environment');
    }

    public function isTest(): bool
    {
        return $this->envIs(Environment::test);
    }

    public function debug(): bool
    {
        return config('app.debug') && !$this->envIs(Environment::production);
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
