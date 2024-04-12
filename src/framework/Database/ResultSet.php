<?php

namespace Baueri\Spire\Framework\Database;

interface ResultSet
{
    /**
     * @return array|object
     */
    public function fetchRow();

    public function getRows(): array;

    public function rowCount(): int;
}
