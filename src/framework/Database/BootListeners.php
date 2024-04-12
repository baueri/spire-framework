<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Database;

use Baueri\Spire\Framework\Bootstrapper;
use Baueri\Spire\Framework\Database\Events\QueryRan;
use Baueri\Spire\Framework\Database\Listeners\LogQueryHistory;

class BootListeners implements Bootstrapper
{
    public function boot(): void
    {
        QueryRan::listen(LogQueryHistory::class);
    }
}
