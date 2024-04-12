<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

interface Directive
{
    public function getPattern(): string;

    public function getReplacement(array $matches): string;
}
