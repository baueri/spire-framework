<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

class IfDirective implements Directive
{
    public function getName(): string
    {
        return 'if';
    }

    public function getReplacement(array $matches): string
    {
        if (str_starts_with($matches[0], '@end')) {
            return '<?php endif; ?>';
        }

        if (str_starts_with($matches[0], '@elseif')) {
            return '<?php elseif(' . $matches[2] . '): ?>';
        }

        if (str_starts_with($matches[0], '@else')) {
            return '<?php else: ?>';
        }

        return '<?php if(' . $matches[1] . '): ?>';
    }

    public function getPattern(): string
    {
        return '/@if\((.*)\)|@elseif\((.*)\)|(@else)|(@endif)/';
    }
}
