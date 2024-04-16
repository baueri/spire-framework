<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database;

use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Database\PaginatedResultSet;

class PaginatedResultSetTest extends TestCase
{
    public function testPaginatedResultSet(): void
    {
        $items = ['item1', 'item2', 'item3'];
        $perpage = 10;
        $page = 1;
        $total = 0;

        $result = new PaginatedResultSet($items, $perpage, $page, $total);

        $this->assertSame($items, $result->rows());
        $this->assertSame($page, $result->page());
        $this->assertSame($total, $result->total());
        $this->assertSame($perpage, $result->perpage());
    }
}
