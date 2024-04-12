<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View;

use Baueri\Spire\Framework\Support\FileCache;

readonly class ViewCache
{
    public function __construct(
        protected FileCache $cacheDir
    ) {
    }

    public function getCacheDir(): string
    {
        return $this->cacheDir->path . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR;
    }

    public function cache(string $fileName, ?string $content): bool
    {
        $cachedFileName = $this->getCacheFilename($fileName);

        $this->createDirIfNotExists($cachedFileName);

        $content = "<?php //this is the cache file of " . $fileName . " ?>\n" . $content;

        return file_put_contents($cachedFileName, $content) !== false;
    }

    public function getCacheFilename(string $fileName): string
    {
        $hashedFilename = md5($fileName);
        return $this->getCacheDir() . substr($hashedFilename, 0, 2) . DS . md5($fileName) . '.php';
    }

    private function createDirIfNotExists($cachedFileName): void
    {
        if (!is_dir(dirname($cachedFileName))) {
            mkdir(dirname($cachedFileName), 0775, true);
        }
    }

    public function shouldUpdateFile(string $fileName): bool
    {
        $cacheFilePath = $this->getCacheFilename($fileName);

        return config('app.do_cache') || !file_exists($cacheFilePath) || filemtime($fileName) > filemtime($cacheFilePath);
    }
}
