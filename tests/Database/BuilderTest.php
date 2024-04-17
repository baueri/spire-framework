<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Database;

use Baueri\Spire\Framework\Database\PDO\PDODatabase;
use Baueri\Spire\Framework\Database\PDO\PDODatabaseFactory;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Database\Builder;
use ReflectionProperty;

class BuilderTest extends TestCase
{
    protected Builder $builder;
    protected static PDODatabase $database;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        touch($file = __DIR__ . '/BuilderTest.sq3');

        static::$database = PDODatabaseFactory::create([
            'driver' => 'sqlite',
            'host' => $file
        ]);

        static::$database->execute('CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT, age INTEGER)');
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        unlink(__DIR__ . '/BuilderTest.sq3');
    }

    protected function setUp(): void
    {
        parent::setUp();

        static::$database->execute('delete from users');

        static::$database->execute("
            INSERT INTO 'users' ('name', 'email', 'age')
                VALUES('John Doe', 'john.doe@example.com', '30'), ('Jane Doe', 'jane.doe@example.com', '30')
        ");

        $this->builder = new Builder(static::$database);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testGet(): void
    {
        $result = $this->builder->from('users')->get();

        $this->assertEquals([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'age' => 30],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane.doe@example.com', 'age' => 30],
        ], $result);
    }

    public function testCollect(): void
    {
        $result = $this->builder->from('users')->collect();

        $this->assertEquals(collect([
            ['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'age' => 30],
            ['id' => 2, 'name' => 'Jane Doe', 'email' => 'jane.doe@example.com', 'age' => 30],
        ]), $result);
    }

    public function testPluck(): void
    {
        $result = $this->builder->from('users')->pluck('name');

        $this->assertEquals(['John Doe', 'Jane Doe'], $result);
    }

    public function testCount(): void
    {
        $result = $this->builder->from('users')->count();

        $this->assertEquals(2, $result);
    }

    public function testCountWhenUsingGroupBy(): void
    {
        $result = $this->builder->from('users')->groupBy('age')->count();

        $this->assertEquals(1, $result);
    }

    public function testCountBy(): void
    {
        $result = $this->builder->from('users')->countBy('age');

        $this->assertEquals(['30' => 2], $result);
    }

    public function testEach(): void
    {
        $names = [];
        $this->builder->from('users')->each(function ($row) use (&$names) {
            $names[] = $row['name'];
        });

        $this->assertEquals(['John Doe', 'Jane Doe'], $names);
    }

    #[DataProvider('whenDataProvider')]
    public function testWhen($expression, $expected): void
    {
        $called = false;
        $callback = function (Builder $query) use (&$called) {
            $called = true;
        };

        $this->builder->from('users')->when($expression, $callback);
        $this->assertSame($expected, $called);
    }

    public static function whenDataProvider(): array
    {
        return [
            [true, true],
            [false, false],
        ];
    }

    public function testSelect(): void
    {
        $result = $this->builder->from('users')->select('name')->getSelect();

        $this->assertEquals(['name'], $result);

        $result = $this->builder->from('users')->select(['name', 'email'])->getSelect();

        $this->assertEquals(['name', 'email'], $result);
    }

    public function testDistinct(): void
    {
        $result = $this->builder->from('users')->distinct()->select('age')->get();

        $this->assertEquals([['age' => 30]], $result);
    }

    public function testAddSelect(): void
    {
        $result = $this->builder->from('users')->select('name')->addSelect('age')->getSelect();

        $this->assertEquals(['name', 'age'], $result);
    }

    public function testAddSelectUnique(): void
    {
        $result = $this->builder->from('users')->select('name')->addSelect('name')->getSelect();

        $this->assertEquals(['name'], $result);
    }

    #[DataProvider('whereDataProvider')]
    public function testWhere($where, $expected): void
    {
        $result = $this->builder->from('users')->where(...$where)->getWhere();
        $this->assertEquals($expected, $result);
    }

    public static function whereDataProvider(): array
    {
        return [
            [['name', 'John Doe'], [['name', '=', 'John Doe', 'and']]],
            [['name', '<>', 'John Doe'], [['name', '<>', 'John Doe', 'and']]],
            [['name', '<>', 'John Doe', 'and'], [['name', '<>', 'John Doe', 'and']]],
            [['name', '<>', 'John Doe', 'or'], [['name', '<>', 'John Doe', 'or']]],
        ];
    }

    public function testWhereCallback(): void
    {
        $result = $this->builder->from('users')->where($closure = function (Builder $query) {
            $query->where('name', 'John Doe');
        })->getWhere();

        $this->assertEquals([[$closure, null, null, 'and']], $result);
    }

    public function testWhereRaw(): void
    {
        $result = $this->builder->from('users')->whereRaw('name = ?', ['John Doe'])->getWhere();

        $this->assertEquals([['name = ?', null, ['John Doe'], 'and']], $result);
    }

    public function testWhereInSet(): void
    {
        $result = $this->builder->from('users')->whereInSet('name', 'John Doe')->getWhere();

        $this->assertEquals([['FIND_IN_SET(?, name)', null, ['John Doe'], 'and']], $result);
    }

    public function testWhereNull(): void
    {
        $result = $this->builder->from('users')->whereNull('name')->getWhere();

        $this->assertEquals([['name IS NULL', null, [], 'and']], $result);
    }

    public function testWhereNotNull(): void
    {
        $result = $this->builder->from('users')->whereNotNull('name')->getWhere();

        $this->assertEquals([['name IS NOT NULL', null, [], 'and']], $result);
    }

    public function testWhereIn(): void
    {
        $result = $this->builder->from('users')->whereIn('name', ['John Doe', 'Jane Doe'])->getWhere();

        $this->assertEquals([['name', 'in', ['John Doe', 'Jane Doe'], 'and']], $result);
    }

    public function testWhereNotIn(): void
    {
        $result = $this->builder->from('users')->whereNotIn('name', ['John Doe', 'Jane Doe'])->getWhere();

        $this->assertEquals([['name', 'not in', ['John Doe', 'Jane Doe'], 'and']], $result);
    }

    public function testWherePast(): void
    {
        $result = $this->builder->from('users')->wherePast('age')->getWhere()[0];

        $this->assertEquals('age', $result[0]);
        $this->assertEquals('<', $result[1]);
        $this->assertEquals('and', $result[3]);

        $this->assertInstanceOf(DateTime::class, $result[2]);
    }

    #[DataProvider('buildWhereDataProvider')]
    public function testBuildWhere($where, $expected): void
    {
        $this->builder->from('users');

        $where($this->builder);

        $result = $this->builder->buildWhere();

        $this->assertEquals($expected, $result);
    }

    public static function buildWhereDataProvider(): array
    {
        return [
            'simple where' => [fn (Builder $query) => $query->where('name', 'John Doe')->where('age', '>', 30), 'name = ? and age > ?'],
            'where in' => [fn (Builder $query) => $query->whereIn('name', ['John Doe', 'Jane Doe']), 'name in (?,?)'],
            'closure' => [fn (Builder $query) => $query->where(fn (Builder $query) => $query->where('name', 'John Doe')), '(name = ?)'],
            'raw' => [fn (Builder $query) => $query->whereRaw('name = ?', ['John Doe']), 'name = ?'],
        ];
    }

    public function testFirst(): void
    {
        $result = $this->builder->from('users')->first();

        $this->assertEquals(['id' => 1, 'name' => 'John Doe', 'email' => 'john.doe@example.com', 'age' => 30], $result);
    }

    public function testFetchFirst(): void
    {
        $result = $this->builder->from('users')->fetchFirst();

        $this->assertEquals(1, $result);

        $result = $this->builder->from('users')->fetchFirst('name');

        $this->assertEquals('John Doe', $result);
    }

    public function testLimit(): void
    {
        $result = $this->builder->from('users')->limit(1)->get();

        $this->assertEquals(1, count($result));
    }

    #[DataProvider('paginateDataProvider')]
    public function testPaginate($limit, $page, $expected): void
    {
        static::$database->execute('delete from users');
        static::$database->execute("
            INSERT INTO 'users' ('name', 'email', 'age')
                VALUES('John', 'a', '10'), ('Jane', 'b', '20'), ('Alexa', 'c', '30'), ('Peter', 'd', '40'), ('George', 'e', '50')");

        $result = $this->builder->from('users')->paginate($limit, $page);

        $this->assertSame($expected, $result->pluck('name')->all());
    }

    public static function paginateDataProvider(): array
    {
        return [
            'first page, two per page' => [2, 1, ['John', 'Jane']],
            'second page, two per page' => [2, 2, ['Alexa', 'Peter']],
            'third page, two per page' => [2, 3, ['George']],
        ];
    }

    #[DataProvider('orderByDataProvider')]
    public function testOrderBy($data, $expected): void
    {
        $this->builder->from('users')->orderBy(...$data);

        $reflection = new ReflectionProperty($this->builder, 'orderBy');

        $this->assertEquals($expected, $reflection->getValue($this->builder));
    }

    public static function orderByDataProvider(): array
    {
        return [
            'simple string' => [['name'], ['name']],
            'string with direction' => [['name desc'], ['name desc']],
            'array' => [[['name', 'age'], 'asc'], ['name asc', 'age asc']],
        ];
    }

    #[DataProvider('groupByDataProvider')]
    public function testGroupBy($groupBy, $expected): void
    {
        $this->builder->from('users')->groupBy($groupBy);

        $reflection = new ReflectionProperty($this->builder, 'groupBy');

        $this->assertEquals($expected, $reflection->getValue($this->builder));
    }

    public static function groupByDataProvider(): array
    {
        return [
            'simple string' => ['name', ['name']],
            'array' => [['name', 'age'], ['name', 'age']],
        ];
    }

    public function testHaving(): void
    {
        $this->builder->from('users')->having('age > ?', [20]);

        $reflection = new ReflectionProperty($this->builder, 'having');

        $this->assertEquals(['age > ?', [20]], $reflection->getValue($this->builder));
    }
}
