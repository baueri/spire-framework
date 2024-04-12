<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Console\BaseCommands;

 use Baueri\Spire\Framework\Console\Command;
 use Baueri\Spire\Framework\Console\Out;

class SiteDown extends Command
{
    public static function signature(): string
    {
        return 'site:down';
    }

    public static function description(): string
    {
        return 'turn on maintenance mode';
    }

    public function handle(): void
    {
        root()->file('.maintenance')->touch();

        Out::warning('The site is now down for maintenance');
    }
}
