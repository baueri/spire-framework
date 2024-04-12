<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

abstract class AtDirective implements Directive
{
    abstract public function getName(): string;

    public function getPattern(): string
    {
        return "/@{$this->getName()}\(([^\)]+\)?)?\)|@end{$this->getName()}/";
    }
}
