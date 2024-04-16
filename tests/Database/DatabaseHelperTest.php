<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database;

use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Database\DatabaseHelper;

class DatabaseHelperTest extends TestCase
{
    public function testGetQueryWithBindings(): void
    {
        $query = 'SELECT * FROM table WHERE id = ? AND name = ? and type = ?';
        $bindings = [1, 'test', EnumStub::one];

        $result = DatabaseHelper::getQueryWithBindings($query, $bindings);

        $this->assertSame("SELECT * FROM table WHERE id = '1' AND name = 'test' and type = 'one'", $result);
    }
}

enum EnumStub
{
    case one;
    case two;
}
