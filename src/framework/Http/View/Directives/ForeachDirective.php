<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

class ForeachDirective extends AtDirective
{
    public function getName(): string
    {
        return 'foreach';
    }

    public function getReplacement(array $matches): string
    {
        if($matches[0] == '@endforeach') {
            return '<?php endforeach; ?>';
        }
        
        return '<?php foreach(' . $matches[1] . '): ?>';
    }

}
