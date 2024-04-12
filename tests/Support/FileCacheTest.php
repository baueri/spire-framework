<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Support;

use Baueri\Spire\Framework\Support\FileCache;
use PHPUnit\Framework\TestCase;

class FileCacheTest extends TestCase
{
    public function testGet(): void
    {
        $cache = new FileCache('cache');
        $cache->save('test.txt', 'Hello, World!');

        $this->assertEquals('Hello, World!', $cache->get('test.txt'));
    }

    public function testSave(): void
    {
        $cache = new FileCache('cache');
        $cache->save('test.txt', 'Hello, World!');

        $this->assertEquals('Hello, World!', file_get_contents('cache/test.txt'));
    }

    public function testClear(): void
    {
        $cache = new FileCache('cache');
        $cache->save('test.txt', 'Hello, World!');

        $this->assertTrue($cache->clear());
        $this->assertFileDoesNotExist('cache/test.txt');
    }
}
