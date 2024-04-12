<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http;

use Baueri\Spire\Framework\Middleware\Middleware;
use Baueri\Spire\Framework\Traits\BootsClass;

class Controller
{
    use BootsClass;

    protected array $middleware = [];

    public function __construct(protected Request $request)
    {
        $this->bootClass();
        foreach ($this->middleware as $middleware) {
            $this->middleware($middleware);
        }
    }

    public function middleware(string|Middleware $middleware): void
    {
        if ($middleware instanceof Middleware) {
            $middleware->handle();
            return;
        }

        app()->make($middleware)->handle();
    }
}
