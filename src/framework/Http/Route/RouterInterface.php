<?php

namespace Baueri\Spire\Framework\Http\Route;

use Baueri\Spire\Framework\Http\RequestMethod;
use Baueri\Spire\Framework\Support\Collection;

interface RouterInterface
{
    /**
     * @return Collection<Route>
     */
    public function getRoutes(): Collection;

    public function find(string $uri, null|RequestMethod $method = null): ?Route;

    /**
     * @param string $name
     * @param array $args
     * @param bool $withHost
     * @return string
     */
    public function route(string $name, mixed $args = null, bool $withHost = true): string;

    public function addGlobalArg($name, $value);

    public function addRoute(Route $route): static;
}
