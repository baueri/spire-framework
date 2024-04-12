<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Support;

use Baueri\Spire\Framework\Support\Arr;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrTest extends TestCase
{
    public function testEach(): void
    {
        $result = 0;

        Arr::each([1, 2, 3], function ($item) use (&$result) {
            $result += $item;
        });

        $this->assertEquals(6, $result);
    }

    public function testMap(): void
    {
        $result = Arr::map([1, 2, 3], fn ($item) => $item * 2);

        $this->assertEquals([2, 4, 6], $result);
    }

    public function testFilter(): void
    {
        $result = Arr::filter([1, 2, 3, 4, 5, 6], fn ($item) => $item % 2 === 0);

        $this->assertEquals([1 => 2, 3 => 4, 5 => 6], $result);
    }

    public function testFilterByKey(): void
    {
        $result = Arr::filterByKey([1, 2, 3], fn ($key) => $key % 2 === 0);

        $this->assertEquals([0 => 1, 2 => 3], $result);
    }

    public function testGet(): void
    {
        $result = Arr::get(['foo' => 'bar'], 'foo');

        $this->assertEquals('bar', $result);
    }

    public function testHas(): void
    {
        $this->assertTrue(Arr::has(['foo' => 'bar'], 'foo'));
        $this->assertFalse(Arr::has(['foo' => 'bar'], 'baz'));
        $this->assertTrue(Arr::has([['foo' => 'bar'], ['foo' => 'baz']], 'foo', 'bar'));
        $this->assertFalse(Arr::has([['foo' => 'bar'], ['foo' => 'baz']], 'foo', 'qux'));
    }

    public function testHasNotWithNull(): void
    {
        $result = Arr::has(['foo' => null], 'foo');

        $this->assertTrue($result);
    }

    public function testHasNotWithEmptyArray(): void
    {
        $result = Arr::has(['foo' => []], 'foo');

        $this->assertTrue($result);
    }

    public function testHasNotWithEmptyString(): void
    {
        $result = Arr::has(['foo' => ''], 'foo');

        $this->assertTrue($result);
    }

    public function testHasNotWithFalse(): void
    {
        $result = Arr::has(['foo' => false], 'foo');

        $this->assertTrue($result);
    }

    public function testRandom(): void
    {
        $result = Arr::random([1, 2, 3]);

        $this->assertContains($result, [1, 2, 3]);
    }

    public function testPluck(): void
    {
        $result = Arr::pluck([['foo' => 'bar'], ['foo' => 'baz']], 'foo');

        $this->assertEquals(['bar', 'baz'], $result);
    }

    #[DataProvider('onlyDataProvider')]
    public function testOnly($data, $key, $expected): void
    {
        $result = Arr::only($data, $key);

        $this->assertEquals($expected, $result);
    }

    public static function onlyDataProvider(): array
    {
        return [
            'one key' => [
                ['foo' => 'bar', 'baz' => 'qux'],
                'foo',
                ['foo' => 'bar'],
            ],
            'two keys' => [
                ['foo' => 'bar', 'baz' => 'qux'],
                ['foo', 'baz'],
                ['foo' => 'bar', 'baz' => 'qux'],
            ],
            'non-existing key' => [
                ['foo' => 'bar'],
                'baz',
                [],
            ],
            'empty array' => [
                [],
                'foo',
                [],
            ]
        ];
    }

    public function testGetItemValue(): void
    {
        $result = Arr::getItemValue(['foo' => 'bar'], 'foo');

        $this->assertEquals('bar', $result);
    }

    public function getItemValueWithObject(): void
    {
        $object = new class {
            public $foo = 'bar';
        };

        $result = Arr::getItemValue($object, 'foo');

        $this->assertEquals('bar', $result);
    }

    public function getItemValueWhenNoKey(): void
    {
        $result = Arr::getItemValue(['foo' => 'bar']);

        $this->assertEquals(['foo' => 'bar'], $result);
    }

    public function testItemValueWhenNoKeyObject(): void
    {
        $object = new class {
            public $foo = 'bar';
        };

        $result = Arr::getItemValue($object);

        $this->assertEquals($object, $result);
    }

    #[DataProvider('sumDataProvider')]
    public function testSum($array, $column, $expected): void
    {
        $result = Arr::sum($array, $column);

        $this->assertEquals($expected, $result);
    }

    public static function sumDataProvider(): array
    {
        return [
            'sum of integers' => [
                [1, 2, 3],
                null,
                6,
            ],
            'sum of floats' => [
                [1.1, 2.2, 3.3],
                null,
                6.6,
            ],
            'sum of integers with column' => [
                [['foo' => 1], ['foo' => 2], ['foo' => 3]],
                'foo',
                6,
            ],
            'sum of floats with column' => [
                [['foo' => 1.1], ['foo' => 2.2], ['foo' => 3.3]],
                'foo',
                6.6,
            ],
        ];
    }

    #[DataProvider('wrapDataProvider')]
    public function testWrap($data, $expected): void
    {
        $result = Arr::wrap($data);

        $this->assertEquals($expected, $result);
    }

    public static function wrapDataProvider(): array
    {
        $obj = new class {
            public $foo = 'bar';
        };

        return [
            'null' => [null, []],
            'object' => [$obj, [$obj]],
            'callable' => [
                fn () => 'foo',
                [fn () => 'foo'],
            ],
            'array' => [
                ['foo' => 'bar'],
                ['foo' => 'bar'],
            ],
            'string' => ['foo', ['foo']]
        ];
    }

    #[DataProvider('fromListDataProvider')]
    public function testFromList($list, $separator, $expected): void
    {
        $result = Arr::fromList($list, $separator);

        $this->assertEquals($expected, $result);
    }

    public static function fromListDataProvider(): array
    {
        return [
            'null' => [null, ',', []],
            'empty string' => ['', ',', []],
            'comma separated' => ['foo,bar,baz', ',', ['foo', 'bar', 'baz']],
            'pipe separated' => ['foo|bar|baz', '|', ['foo', 'bar', 'baz']],
        ];
    }
}
