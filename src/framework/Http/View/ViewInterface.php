<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View;

interface ViewInterface
{
    public function view(string $view, array $args = []): string;

    public function getSection(): Section;
}
