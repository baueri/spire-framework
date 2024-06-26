<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

class RouteDirective extends AtDirective
{
    public function getName(): string
    {
        return 'route';
    }

    public function getReplacement(array $matches): string
    {
        return '<?php echo route(' . $matches[1] . '); ?>';
    }
}
