<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database\PDO;

use Baueri\Spire\Framework\Database\PDO\PDODatabase;
use Exception;
use PDO;
use PDOStatement;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class PDODatabaseTest extends TestCase
{
    private PDO $pdo;
    private PDODatabase $database;
    private PDOStatement $statement;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->statement = $this->createMock(PDOStatement::class);
        $this->statement->method('execute')->willReturn(true);
        $this->database = new PDODatabase($this->pdo);

        $this->pdo->method('prepare')->willReturn($this->statement);
    }

    public function testExecutesQuerySuccessfully(): void
    {
        $query = 'SELECT * FROM users';
        $bindings = ['id' => 1];

        $this->statement->expects($this->once())->method('execute');
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $this->database->execute($query, ...$bindings);
    }

    public function testSelect(): void
    {
        $query = 'SELECT * FROM users where id = :id';
        $bindings = ['id' => 1];
        $rows = [['id' => 1, 'name' => 'John Doe']];

        $this->statement->method('fetchAll')->willReturn($rows);
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $result = $this->database->select($query, $bindings);

        $this->assertEquals($rows, $result);
    }

    public function testUpdate(): void
    {
        $query = 'UPDATE users SET name = :name WHERE id = :id';
        $bindings = ['id' => 1, 'name' => 'John Doe'];

        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $this->database->update($query, ...$bindings);
    }

    public function testLastInsertId(): void
    {
        $this->pdo->expects($this->once())->method('lastInsertId')->willReturn('1');

        $this->assertEquals(1, $this->database->lastInsertId());
    }

    public function testFirst(): void
    {
        $query = 'SELECT * FROM users where id = :id';
        $bindings = ['id' => 1];
        $row = ['id' => 1, 'name' => 'John Doe'];

        $this->statement->method('fetch')->willReturn($row);
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $result = $this->database->first($query, $bindings);

        $this->assertEquals($row, $result);
    }

    public function testInsert(): void
    {
        $query = 'INSERT INTO users (name) VALUES (:name)';
        $bindings = ['name' => 'John Doe'];

        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);
        $this->pdo->method('lastInsertId')->willReturn('1');

        $this->database->insert($query, $bindings);
    }

    public function testExists(): void
    {
        $query = 'SELECT * FROM users where id = :id';
        $bindings = ['id' => 1];
        $row = ['id' => 1, 'name' => 'John Doe'];

        $this->statement->method('fetch')->willReturn($row);
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $result = $this->database->exists($query, $bindings);

        $this->assertTrue($result);
    }

    public function testFetchColumn(): void
    {
        $query = 'SELECT name FROM users where id = :id';
        $bindings = ['id' => 1];
        $row = ['John Doe'];

        $this->statement->method('fetch')->willReturn($row);
        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $result = $this->database->fetchColumn($query, $bindings);

        $this->assertEquals('John Doe', $result);
    }

    public function testDelete(): void
    {
        $query = 'DELETE FROM users where id = :id';
        $bindings = ['id' => 1];

        $this->pdo->expects($this->once())->method('prepare')->with($query)->willReturn($this->statement);

        $this->database->delete($query, $bindings);
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

    public function testExecutesTransactionSuccessfully(): void
    {
        $callback = function () {
            return 'ran';
        };

        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('commit');

        $this->assertEquals('ran', $this->database->transaction($callback));
    }

    public function testExecutesTransactionAndRollsBackOnFailure(): void
    {
        $callback = function () {
            throw new Exception('Transaction failed');
        };

        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->once())->method('rollBack');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Transaction failed');

        $this->database->transaction($callback);
    }

    #[DataProvider('prepareBindingsDataProvider')]
    public function testPrepareBindings($bindings, $expected): void
    {
        $result = $this->database->prepareBindings($bindings);

        $this->assertSame($expected, $result);
    }

    public static function prepareBindingsDataProvider(): array
    {
        return [
            'empty array' => [[], []],
            'associative array' => [['id' => 1], ['id' => 1]],
            'indexed array' => [['john', '23'], ['john', '23']],
            'enums' => [[ColumnStub::dog], ['dog']],
        ];
    }

    public function testMultipleTransactionBeginning(): void
    {
        $this->pdo->expects($this->once())->method('beginTransaction');
        $this->pdo->expects($this->exactly(2))->method('exec');

        $this->database->beginTransaction();
        $this->database->beginTransaction();
        $this->database->beginTransaction();
    }

    public function testCommit(): void
    {
        $this->database->beginTransaction();

        $this->pdo->expects($this->once())->method('commit');

        $this->database->commit();
    }

    public function testTransactionCounter(): void
    {
        $transactionCounter = new ReflectionProperty($this->database, 'transactionCounter');

        $this->assertEquals(0, $transactionCounter->getValue($this->database));

        $this->database->beginTransaction();

        $this->assertEquals(1, $transactionCounter->getValue($this->database));

        $this->database->beginTransaction();

        $this->assertEquals(2, $transactionCounter->getValue($this->database));

        $this->database->commit();

        $this->assertEquals(1, $transactionCounter->getValue($this->database));

        $this->database->commit();

        $this->assertEquals(0, $transactionCounter->getValue($this->database));

        $this->database->beginTransaction();
        $this->database->beginTransaction();

        $this->assertEquals(2, $transactionCounter->getValue($this->database));
        $this->pdo->expects($this->once())->method('exec')->with('ROLLBACK TO trans2');
        $this->database->rollback();

        $this->assertEquals(1, $transactionCounter->getValue($this->database));
    }
}

enum ColumnStub
{
    case dog;
}
