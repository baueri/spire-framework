<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database\PDO;

use Baueri\Spire\Framework\Database\PDO\PDODatabase;
use Baueri\Spire\Framework\Database\PDO\PDODatabaseFactory;
use PHPUnit\Framework\TestCase;

class PDODatabaseFactoryTest extends TestCase
{
    private string $path = __DIR__ . '/test.sq3';

    protected function setUp(): void
    {
        parent::setUp();

        touch($this->path);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        unlink($this->path);
    }

    public function testCreate(): void
    {
        $database = PDODatabaseFactory::create([
            'driver' => 'sqlite',
            'host' => $this->path
        ]);

        $this->assertInstanceOf(PDODatabase::class, $database);
    }
}
