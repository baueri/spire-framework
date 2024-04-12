<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Database\Repository\Events;

use Baueri\Spire\Framework\Event\Event;

abstract class BaseRepositoryEvent extends Event
{
    public function __construct(
        public $entity
    ) {
    }
}
