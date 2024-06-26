<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support;

use Closure;

class Arr
{
    public static function each(array $items, callable|Closure $callback, ...$params): void
    {
        foreach ($items as $key => $item) {
            if ($callback($item, $key, ...$params) === false) {
                break;
            }
        }
    }

    public static function map(array $items, callable|string $callback, bool $keepKeys = false): array
    {
        $result = [];

        foreach ($items as $key => $item) {
            if (is_object($item) && is_string($callback) && method_exists($item, $callback)) {
                $value = $item->{$callback}();
            } else {
                $value = $callback($item, $key);
            }
            if (!$keepKeys) {
                $result[] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function filter($items, $callback = null, bool $byKey = false): array
    {
        $result = [];

        foreach ($items as $key => $item) {
            if ($byKey ? $callback($key) : $callback($item, $key, $items)) {
                $result[$key] = $item;
            }
        }

        return $result;
    }

    public static function filterByKey($items, $callback): array
    {
        return static::filter($items, $callback, true);
    }

    public static function get($item, $key, $default = null)
    {
        if (is_array($item) && isset($item[$key])) {
            return $item[$key];
        }

        return $item->{$key} ?? $default;
    }

    public static function has($items, $key, $value = null): bool
    {
        if (is_null($value)) {
            return array_key_exists($key, $items);
        }

        foreach ($items as $item) {
            if (static::getItemValue($item, $key) == static::getItemValue($value, $key)) {
                return true;
            }
        }

        return false;
    }

    public static function random($items)
    {
        return $items[array_rand($items)];
    }

    public static function pluck($items, $key, $keyBy = null): array
    {
        $return = [];
        foreach ($items as $item) {
            if ($keyBy) {
                $return[static::getItemValue($item, $keyBy)] = static::getItemValue($item, $key);
            } else {
                $return[] = static::getItemValue($item, $key);
            }
        }

        return $return;
    }

    public static function only(array $items,$only): array
    {
        $only = Arr::wrap($only);
        return static::filterByKey($items, fn ($key) => in_array($key, $only));
    }

    public static function getItemValue($item, $key = null)
    {
        if (!$key) {
            return $item;
        }

        if (is_array($item) && isset($item[$key])) {
            return $item[$key];
        }

        if (is_object($item)) {
            return $item->{$key};
        }

        return $item;
    }

    public static function sum(array $results, string $column = null): float|int
    {
        if (is_numeric(Arr::first($results))) {
            return array_sum($results);
        }

        return static::sum(static::pluck($results, $column));
    }

    public static function first(array $results)
    {
        return $results[key($results)];
    }

    public static function wrap($value): array
    {
        if (is_null($value)) {
            return [];
        }

        if (is_object($value) || is_callable($value)) {
            return [$value];
        }

        return (array) $value;
    }

    public static function fromList(?string $text, string $separator = ','): array
    {
        return match ($text) {
            null, '' => [],
            default => explode($separator, $text)
        };
    }

    public static function except(array $list, array|string|int $except): array
    {
        $except = (array) $except;
        foreach ($list as $key => $item) {
            if (in_array($key, $except)) {
                unset($list[$key]);
            }
        }

        return $list;
    }
}
