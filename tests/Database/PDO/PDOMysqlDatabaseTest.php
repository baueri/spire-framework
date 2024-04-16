<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database\PDO;

use Baueri\Spire\Framework\Database\PDO\PDOMysqlDatabase;
use PDO;
use PDOStatement;
use PHPUnit\Framework\TestCase;

class PDOMysqlDatabaseTest extends TestCase
{
    private PDO $pdo;
    private PDOMysqlDatabase $database;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->database = new PDOMysqlDatabase($this->pdo);
    }

    public function testExecutesQuerySuccessfully(): void
    {
        $query = 'SELECT * FROM users';
        $bindings = ['id' => 1];

        $statement = $this->createMock(PDOStatement::class);
        $statement->method('execute')->with($bindings)->willReturn(true);
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($statement);

        $this->database->execute($query, $bindings);
    }

    public function testBeginsTransactionSuccessfully(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');

        $this->database->beginTransaction();
    }

    public function testCommitsTransactionSuccessfully(): void
    {
        $this->pdo->expects($this->once())->method('commit');
        $this->database->beginTransaction();
        $this->database->commit();
    }

    public function testRollsBackTransactionSuccessfully(): void
    {
        $this->pdo->expects($this->once())->method('rollBack');

        $this->database->beginTransaction();

        $this->database->rollback();
    }

    public function executesTransactionSuccessfully(): void
    {
        $callback = function () {
            return true;
        };

        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');

        $this->database->transaction($callback);
    }

    public function testExecutesTransactionAndRollsBackOnFailure(): void
    {
        $callback = function () {
            throw new \Exception('Transaction failed');
        };

        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('rollBack');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Transaction failed');

        $this->database->transaction($callback);
    }
}
