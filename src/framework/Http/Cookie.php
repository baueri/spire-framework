<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http;

class Cookie
{
    protected static function getTestKey(): string
    {
        return parse_url(get_site_url(), PHP_URL_HOST) . '_test_cookie';
    }

    public static function enabled(): bool
    {
        return isset($_COOKIE[static::getTestKey()]);
    }

    public static function setTestCookie(): void
    {
        $_COOKIE[static::getTestKey()] = 1;
    }
}
