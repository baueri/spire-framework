<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Event;

interface EventListener
{
    public function trigger($event);
}
