<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support\DataFile;

abstract class DataFile
{
    protected static ?string $extension = null;

    protected array $items;

    public function __construct(protected readonly ?string $basePath = null)
    {
//        if ($basePath) {
//            $this->load($basePath);
//        }
    }

    public function get($key, $default = null)
    {
        [$baseName, $index] = static::parseKey($key);

        $fileName = $this->getFileName($baseName);

        $this->load($baseName);

        return static::getValue($this->items[$fileName], $index, $default);
    }

    protected static function parseKey(string $params): array
    {
        $parsed = explode('.', $params, 2);
        if (!isset($parsed[1])) {
            $parsed[] = null;
        }
        return $parsed;
    }

    protected function getFileName(string $baseName): string
    {
        $scope = '';

        if (str_contains($baseName, '::')) {
            $scope = substr($baseName, 0, strpos($baseName, '::')) . DS;
            $baseName = substr($baseName, strpos($baseName, '::') + 2);
        }

        return root()->path($scope . $this->basePath . $baseName) . static::$extension;
    }

    abstract protected function parse($content);

    protected static function getContent(string $filename)
    {
        return file_get_contents($filename);
    }

    private static function getValue(array $items, mixed $index = null, mixed $default = null): mixed
    {
        if (!$index) {
            return $items ?: $default;
        }

        if (isset($items[$index])) {
            return $items[$index];
        }

        $nestedKeys = explode('.', $index);

        foreach ($nestedKeys as $key) {
            $items = $items[$key] ?? null;
        }

        return $items ?: $default;
    }

    public function load($baseName): self
    {
        $fileName = $this->getFileName($baseName);
        if (!isset($this->items[$fileName])) {
            $this->items[$fileName] = static::parse(static::getContent($fileName));
        }

        return $this;
    }

    public function __toString()
    {
        return json_encode($this->items);
    }
}
