<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Entity\Relation;

use Baueri\Spire\Framework\Database\Builder;
use Baueri\Spire\Framework\Entity\Entity;
use Baueri\Spire\Framework\Entity\EntityQueryBuilder;
use Baueri\Spire\Framework\Support\Collection;

readonly class Relation
{
    public ?string $foreignKey;

    public ?string $localKey;

    public function __construct(
        public Has $relationType,
        public EntityQueryBuilder|Builder $queryBuilder,
        public string $relationName,
        ?string $foreignKey = null,
        ?string $localKey = null,
    ) {
        $this->localKey = $localKey ?? $queryBuilder->primaryCol();
        $this->foreignKey = $foreignKey ?? $this->relationName . '_id';
    }

    public function buildQuery(Collection|Entity $instances): EntityQueryBuilder|Builder
    {
        return $this->queryBuilder->whereIn($this->foreignKey, collect($instances)->pluck($this->localKey)->unique()->filter());
    }
}
