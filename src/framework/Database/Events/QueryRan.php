<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Database\Events;

use Baueri\Spire\Framework\Event\Event;

class QueryRan extends Event
{
    protected static array $listeners = [];

    public function __construct(
        public readonly string $query,
        public readonly array $bindings,
        public readonly float $time
    ) {
    }
}
