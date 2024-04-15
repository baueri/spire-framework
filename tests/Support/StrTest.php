<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Tests\Support;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Baueri\Spire\Framework\Support\Str;

class StrTest extends TestCase
{
    #[DataProvider('moreDataProvider')]
    public function testMore(string $text, int $numberOfWords, string $moreText, $expected): void
    {
        $result = Str::more($text, $numberOfWords, $moreText);

        $this->assertEquals($expected, $result);
    }

    public static function moreDataProvider(): array
    {
        return [
            'empty text' => ['', 3, '...', ''],
            'text contains less words' => ['foo bar baz', 5, '...', 'foo bar baz'],
            'text contains exactly the number of words' => ['foo bar baz', 3, '...', 'foo bar baz'],
            'text contains more words' => ['foo bar baz', 2, '...', 'foo bar...'],
            'text without more text postfix' => ['foo bar baz', 2, '', 'foo bar'],
        ];
    }

    #[DataProvider('shortenDataProvider')]
    public function testShorten(?string $text, int $length, string $moreText, $expected): void
    {
        $result = Str::shorten($text, $length, $moreText);

        $this->assertEquals($expected, $result);
    }

    public static function shortenDataProvider(): array
    {
        return [
            'empty text' => [null, 10, '...', ''],
            'text is exactly the length' => ['foo bar baz', 11, '...', 'foo bar baz'],
            'text is longer than the length' => ['foo bar baz', 5, '...', 'foo b...'],
            'text ends with a space' => ['foo bar ', 7, '...', 'foo bar'],
            'text starts with a space' => [' foo bar', 7, '...', 'foo bar'],
        ];
    }

    #[DataProvider('camelDataProvider')]
    public function testCamel($text, $expected): void
    {
        $result = Str::camel($text);

        $this->assertEquals($expected, $result);
    }

    public static function camelDataProvider(): array
    {
        return [
            'empty text' => ['', ''],
            'text with spaces' => ['foo bar baz', 'fooBarBaz'],
            'text with special characters' => ['foo-bar-baz', 'fooBarBaz'],
            'text with special characters and spaces' => ['foo-bar baz', 'fooBarBaz'],
            'text with special characters and spaces and numbers' => ['foo-bar 123 baz', 'fooBar123Baz'],
            'text is already camel case' => ['fooBarBaz', 'fooBarBaz'],
        ];
    }

    #[DataProvider('snakeDataProvider')]
    public function testSnake($text, $expected): void
    {
        $result = Str::snake($text);

        $this->assertEquals($expected, $result);
    }

    public static function snakeDataProvider(): array
    {
        return [
            'empty text' => ['', ''],
            'text with spaces' => ['foo bar baz', 'foo_bar_baz'],
            'text with special characters' => ['foo-bar-baz', 'foo_bar_baz'],
            'text with special characters and spaces' => ['foo-bar baz', 'foo_bar_baz'],
            'text with special characters and spaces and numbers' => ['foo-bar 123 baz', 'foo_bar_123_baz'],
            'text is already snake case' => ['foo_bar_baz', 'foo_bar_baz'],
            'text with uppercase characters' => ['FooBarBaz', 'foo_bar_baz'],
            'text with accents' => ['föö bär báz', 'föö_bär_báz'],
        ];
    }

    #[DataProvider('slugifyDataProvider')]
    public function testSlugify($text, $divider, $expected): void
    {
        $result = Str::slugify($text, $divider);

        $this->assertEquals($expected, $result);
    }

    public static function slugifyDataProvider(): array
    {
        return [
            'empty text' => ['', '-', 'n-a'],
            'text with spaces' => ['foo bar baz', '-', 'foo-bar-baz'],
            'text with special characters' => ['foo-bar-baz', '-', 'foo-bar-baz'],
            'text with special characters and spaces' => ['foo-bar baz', '-', 'foo-bar-baz'],
            'text with special characters and spaces and numbers' => ['foo-bar 123 baz', '-', 'foo-bar-123-baz'],
            'text is already slugified' => ['foo-bar-baz', '-', 'foo-bar-baz'],
            'text with uppercase characters' => ['FooBarBaz', '-', 'foobarbaz'],
            'text with accents' => ['föö bär báz', '-', 'foo-bar-baz'],
        ];
    }

    #[DataProvider('sanitizeDataProvider')]
    public function testSanitize($text, $expected): void
    {
        $result = Str::sanitize($text);

        $this->assertEquals($expected, $result);
    }

    public static function sanitizeDataProvider(): array
    {
        return [
            'empty text' => ['', ''],
            'text with spaces' => ['foo bar baz', 'foo bar baz'],
            'text with special characters' => ['foo-bar-baz', 'foo-bar-baz'],
            'text with special characters and spaces' => ['foo-bar baz', 'foo-bar baz'],
            'text with special characters and spaces and numbers' => ['foo-bar 123 baz', 'foo-bar 123 baz'],
            'text is already sanitized' => ['foo-bar-baz', 'foo-bar-baz'],
            'text with uppercase characters' => ['FooBarBaz', 'FooBarBaz'],
            'text with accents' => ['föö bär báz', 'föö bär báz'],
            'html tags' => ['<p>foo bar baz</p>    <p>foo bar</p>', '<p>foo bar baz</p> <p>foo bar</p>'],
            'remove html comments' => ['foo <!-- bar --> baz', 'foo baz'],
        ];
    }

    #[DataProvider('isEmailDataProvider')]
    public function testIsEmail($text, $expected): void
    {
        $result = Str::isEmail($text);

        $this->assertEquals($expected, $result);
    }

    public static function isEmailDataProvider(): array
    {
        return [
            'empty text' => ['', false],
            'text is not an email' => ['foo bar baz', false],
            'text is an email' => ['is_an_email@gmail.com', true]
        ];
    }

    #[DataProvider('wrapDataProvider')]
    public function testWrap($data, $expected): void
    {
        $result = Str::wrap(...$data);

        $this->assertEquals($expected, $result);
    }

    public static function wrapDataProvider(): array
    {
        return [
            'empty string' => [['', '(', ')'], '()'],
            'string' => [['foo', '(', ')'], '(foo)'],
            'string with different before and after' => [['foo', '(', ']'], '(foo]'],
            'string with same before and after' => [['foo', '(', '('], '(foo('],
            'wrap into one pattern' => [['foo', '#'], '#foo#'],
        ];
    }

    #[DataProvider('endsWithDataProvider')]
    public function testEndsWith($string, $endsWith, $expected): void
    {
        $result = Str::endsWith($string, $endsWith);

        $this->assertEquals($expected, $result);
    }

    public static function endsWithDataProvider(): array
    {
        return [
            'empty string' => ['', '', true],
            'string ends with the same string' => ['foo bar baz', 'baz', true],
            'string does not end with the same string' => ['foo bar baz', 'bar', false],
            'string ends with the same string but different case' => ['foo bar baz', 'BAZ', false],
        ];
    }

    #[DataProvider('startsWithDataProvider')]
    public function testStartsWith($string, $startsWith, $expected): void
    {
        $result = Str::startsWith($string, $startsWith);

        $this->assertEquals($expected, $result);
    }

    public static function startsWithDataProvider(): array
    {
        return [
            'empty string' => ['', '', true],
            'string starts with the same string' => ['foo bar baz', 'foo', true],
            'string does not start with the same string' => ['foo bar baz', 'bar', false],
            'string starts with the same string but different case' => ['foo bar baz', 'FOO', false],
        ];
    }

    #[DataProvider('maskDataProvider')]
    public function testMask($text, $keep, $mask, $expected): void
    {
        $result = Str::mask($text, $keep, $mask);

        $this->assertEquals($expected, $result);
    }

    public static function maskDataProvider(): array
    {
        return [
            'empty text' => ['', 3, '*', ''],
            'text is shorter than the keep length' => ['foo', 5, '*', 'foo'],
            'text is longer than the keep length' => ['foo bar', 5, '*', 'foo b**'],
            'text is exactly the keep length' => ['foo bar', 7, '*', 'foo bar'],
            'mask with different character' => ['foo bar baz', 5, '-', 'foo b------'],
        ];
    }

    #[DataProvider('maskEmailDataProvider')]
    public function testMaskEmail($data, $expected): void
    {
        $result = Str::maskEmail(...$data);

        $this->assertEquals($expected, $result);
    }

    public static function maskEmailDataProvider(): array
    {
        return [
            'mask all text before @' => [['test@example.com'], '****@example.com'],
            'empty email' => [['', 3], ''],
            'email without @' => [['foo.bar.baz', 3], ''],
            'email with @ and with exact length as keep' => [['test@example.com', 4], 'test@example.com'],
            'email with @ and with longer length than keep' => [['testlonger@example.com', 4], 'test******@example.com']
        ];
    }
}
