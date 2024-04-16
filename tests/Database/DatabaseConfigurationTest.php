<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database;

use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Database\DatabaseConfiguration;

class DatabaseConfigurationTest extends TestCase
{
    public function testDatabaseConfiguration(): void
    {
        $config = new DatabaseConfiguration(
            host: 'localhost',
            user: 'root',
            password: 'root',
            database: 'test',
            charset: 'utf8',
            port: 3306);

        $this->assertSame('localhost', $config->host);
        $this->assertSame('root', $config->user);
        $this->assertSame('root', $config->password);
        $this->assertSame('test', $config->database);
        $this->assertSame('utf8', $config->charset);
        $this->assertSame(3306, $config->port);
    }

    public function testDatabaseConfigurationDebugInfo(): void
    {
        $config = new DatabaseConfiguration(
            host: 'localhost',
            user: 'root',
            password: 'root',
            database: 'test',
            charset: 'utf8',
            port: 3306);
        
        $this->assertSame([
            'host' => 'localhost',
            'user' => 'root',
            'password' => '***',
            'database' => 'test',
            'charset' => 'utf8',
            'port' => 3306
        ], $config->__debugInfo());
    }
}
