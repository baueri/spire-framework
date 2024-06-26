<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Database\PDO;

use Baueri\Spire\Framework\Database\ResultSet;
use PDO;
use PDOStatement;

class PDOResultSet implements ResultSet
{
    public const int FETCH_STYLE = PDO::FETCH_ASSOC;

    private PDOStatement $statement;

    public function __construct(PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function fetchRow(int $fetchStyle = self::FETCH_STYLE)
    {
        return $this->statement->fetch($fetchStyle) ?: null;
    }

    public function getRows(int $fetchStyle = self::FETCH_STYLE): array
    {
        return $this->statement->fetchAll($fetchStyle);
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }
}
