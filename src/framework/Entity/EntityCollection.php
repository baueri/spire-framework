<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Entity;

use Baueri\Spire\Framework\Support\Collection;

/**
 * @phpstan-template T of Entity
 * @phpstan-extends Collection<T>
 */
class EntityCollection extends Collection
{
    public function getIds(): Collection
    {
        return $this->map(fn ($entity) => $entity->getId());
    }
}
