<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View\Directives;

class SectionDirective extends AtDirective
{
    public function getName(): string
    {
        return 'section';
    }

    /**
     * @param array $matches
     * @return string
     */
    public function getReplacement(array $matches): string
    {
        if (str_starts_with($matches[0], '@end')) {
            return '<?php }); ?>';
        }
        if (preg_match('/[\'\"],/', $matches[1])) {
            $endPart = '); ';
        } else {
            $endPart = ', function($args) { extract($args);';
        }

        return '<?php $__env->getSection()->add(' . $matches[1] . $endPart . ' ?>';
    }
}
