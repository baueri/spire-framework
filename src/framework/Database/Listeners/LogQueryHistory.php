<?php

namespace Baueri\Spire\Framework\Database\Listeners;

use Baueri\Spire\Framework\Database\Events\QueryRan;
use Baueri\Spire\Framework\Database\QueryLog;
use Baueri\Spire\Framework\Event\EventListener;

class LogQueryHistory implements EventListener
{
    public function __construct(
        protected readonly QueryLog $queryHistory
    ) {
    }

    /**
     * @param QueryRan $event
     */
    public function trigger($event): void
    {
        $this->queryHistory->pushQuery($event->query, $event->bindings, $event->time);
    }
}
