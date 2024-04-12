<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Event;

class EventDispatcher
{
    public static function dispatch(Event $event): void
    {
        foreach ($event::getListeners() as $listener) {
            if (is_callable($listener)) {
                $listener($event);
                return;
            }

            app()->make($listener)->trigger($event);
        }
    }
}
