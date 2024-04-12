<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View;

abstract class Component
{
    abstract public function render(): string;

    public function __toString(): string
    {
        return $this->render();
    }
}
