<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View;

use Exception;
use Baueri\Spire\Framework\Event\EventDispatcher;
use Baueri\Spire\Framework\Http\View\Exception\ViewNotFoundException;

class View implements ViewInterface
{
    protected static array $envVariables = [];

    private ViewCache $cacheDriver;

    private Section $section;

    private ViewParser $parser;

    public function __construct(ViewCache $cacheDriver, Section $section, ViewParser $parser)
    {
        $this->cacheDriver = $cacheDriver;
        $this->section = $section;
        $this->parser = $parser;
    }

    /**
     * @throws ViewNotFoundException
     * @throws Exception
     */
    public function view(string $view, array $args = [], array $additional_args = []): string
    {
        $filePath = static::getPath($view);

        if (!file_exists($filePath)) {
            throw new ViewNotFoundException('view file not found: ' . $filePath);
        }

        return $this->getContentAndDoCache($filePath, array_merge($additional_args, $args));
    }

    public static function exists(string $view): bool
    {
        return file_exists(static::getPath($view));
    }

    public static function getPath(string $view): string
    {
        $viewPath = str_replace('.', DS, $view);

        if (str_contains($view, ':')) {
            [$dirPath, $viewPath] = explode(':', $viewPath);
            $dirPath = rtrim(config("view.view_sources.{$dirPath}"), DS) . DS;
        } else {
            $dirPath = config('app.views_dir');
        }

        return $dirPath . $viewPath . '.php';
    }

    /**
     * @throws Exception
     */
    protected function getContentAndDoCache(string $filePath, array $args = []): string
    {
        if ($this->cacheDriver->shouldUpdateFile($filePath)) {
            $content = $this->parser->parse(file_get_contents($filePath));
            $this->cacheDriver->cache($filePath, $content);
        }

        $args['__env'] = $this;

        $args = array_merge(static::$envVariables, $args);

        extract($args);

        ob_start();

        $cachedFilename = $this->cacheDriver->getCacheFilename($filePath);
        EventDispatcher::dispatch(new ViewLoaded($filePath, $cachedFilename));

        include $cachedFilename;

        return (string) ob_get_clean();
    }

    public function getSection(): Section
    {
        return $this->section;
    }

    public static function setVariable(string $key, $value): void
    {
        static::$envVariables[$key] = $value;
    }

    /**
     * @psalm-template T
     * @psalm-param $component T class-string<T>
     * @param string $component
     * @param string|null $expression
     * @return string
     */
    public static function component(string $component, ?string $expression = null): string
    {
        return "<?php echo app()->make({$component}::class)->render({$expression}); ?>";
    }
}
