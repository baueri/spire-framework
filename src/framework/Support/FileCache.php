<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support;

readonly class FileCache
{
    public function __construct(
        public string $path = __DIR__ . '/../../.cache'
    ) {
    }

    public function get(string $path): ?string
    {
        $file = $this->path . '/' . $path;

        if (!file_exists($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    public function save(string $path, string $content): void
    {
        $file = $this->path . '/' . $path;

        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0777, true);
        }

        file_put_contents($file, $content);
    }

    public function clear(): bool
    {
        return rrmdir($this->path . '/');
    }
}
