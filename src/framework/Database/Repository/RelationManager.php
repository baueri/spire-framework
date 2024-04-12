<?php


namespace Baueri\Spire\Framework\Database\Repository;


use Baueri\Spire\Framework\Support\Collection;

class RelationManager
{
    /**
     * @var Collection
     */
    protected $relations;

    /**
     * RelationManager constructor.
     */
    public function __construct()
    {
        $this->relations = new Collection();
    }
}
