<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Console\BaseCommands;

 use Baueri\Spire\Framework\Console\Command;
 use Baueri\Spire\Framework\Console\Out;

class SiteUp extends Command
{
    public static function signature(): string
    {
        return 'site:up';
    }

    public static function description(): string
    {
        return 'turn off maintenance mode';
    }

    public function handle(): void
    {
        root()->file('.maintenance')->delete();

        Out::success('The site is now on line');
    }
}
