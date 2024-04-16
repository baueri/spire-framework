<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database;

use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Database\QueryLog;

class QueryLogTest extends TestCase
{
    public function testQueryLog(): void
    {
        $query = 'SELECT * FROM table WHERE id = ? AND name = ? and type = ?';
        $bindings = [1, 'test', EnumStub::one];

        $queryLog = new QueryLog();

        $queryLog->pushQuery($query, $bindings, 0.1);

        $this->assertSame([
            [
                "SELECT * FROM table WHERE id = ? AND name = ? and type = ?",
                [1, 'test', EnumStub::one],
                0.1
            ]
        ], $queryLog->getQueryLog()->all());
    }

    public function testQueryLogGetLastQuery(): void
    {
        $queryLog = new QueryLog();

        $queryLog->pushQuery('SELECT * FROM table WHERE id = ? AND name = ? and type = ?', [1, 'a', 'b'], 0.1);
        $queryLog->pushQuery('SELECT * FROM table_2 WHERE id = ? AND mode = ?', [1, 'c', 'd'], 0.3);

        $this->assertSame([
            'SELECT * FROM table_2 WHERE id = ? AND mode = ?',
            [1, 'c', 'd'],
            0.3
        ], $queryLog->getLastQuery());
    }

    public function testQueryLogGetExecutionTime(): void
    {
        $queryLog = new QueryLog();

        $queryLog->pushQuery('SELECT * FROM table WHERE id = ? AND name = ? and type = ?', [1, 'a', 'b'], 0.1);
        $queryLog->pushQuery('SELECT * FROM table_2 WHERE id = ? AND mode = ?', [1, 'c', 'd'], 0.3);

        $this->assertSame(0.4, $queryLog->getExecutionTime());
    }
}
