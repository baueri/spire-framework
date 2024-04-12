<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Traits;

trait DispatchesEvents
{
    protected array $listeners = [];

    public function on(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    /**
     * @param string $event
     * @param array $payload
     */
    protected function runEvent(string $event, array $payload = []): void
    {
        $listeners = $this->listeners[$event] ?? [];

        foreach ($listeners as $listener) {
            $listener(...$payload);
        }
    }
}
