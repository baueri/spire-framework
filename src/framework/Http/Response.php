<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http;

class Response
{
    public static function asJson(): void
    {
        header('Content-Type: application/json');
    }

    public static function headers(): array
    {
        $headers = headers_list();
        foreach ($headers as $i => $row) {
            [$key, $value] = explode(': ', $row);
            unset($headers[$i]);
            $headers[$key] = $value;
        }

        return $headers;
    }

    public static function setHeader($name, $value): void
    {
        header("$name: $value");
    }

    public static function setStatusCode($code): void
    {
        http_response_code((int) $code);
    }

    public static function getHeader($name)
    {
        return static::headers()[$name] ?? null;
    }

    public static function contentTypeIsJson(): bool
    {
        return static::getHeader('Content-Type') == 'application/json';
    }
}
