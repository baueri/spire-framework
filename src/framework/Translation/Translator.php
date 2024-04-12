<?php

namespace Baueri\Spire\Framework\Translation;

use Baueri\Spire\Framework\Event\EventDispatcher;
use Baueri\Spire\Framework\Exception\InvalidTranslationFileException;
use Baueri\Spire\Framework\Support\Collection;

class Translator
{
    private Collection $cache;

    private string $defaultLang = '';

    public function __construct()
    {
        $this->cache = new Collection();
    }

    public function setDefaultLang(string $lang): Translator
    {
        $this->defaultLang = $lang;

        return $this;
    }

    public function translate(string $key, ?string $lang = null): string
    {
        $lang = $lang ?: $this->defaultLang;

        if (!isset($this->cache[$lang])) {
            $this->load($lang);
        }

        $translated = $this->cache[$lang][$key] ?? null;

        if ($translated) {
            return $translated;
        }

        if ($lang === 'hu') {
            return $key;
        }

        EventDispatcher::dispatch(new TranslationMissing($lang, $key));

        return "$key";
    }

    public function translateF(string $key, ...$args): string
    {
        return sprintf($this->translate($key), ...$args);
    }

    /**
     * @throws InvalidTranslationFileException
     */
    private function load(string $lang): void
    {
        $fileName = root()->resources('lang' . DIRECTORY_SEPARATOR)->path($lang . '.json');

        if (!file_exists($fileName)) {
            throw new InvalidTranslationFileException("Could not find translation file: {$fileName}");
        }

        $content = file_get_contents($fileName);

        if (!is_null($parsed = json_decode($content, true))) {
            $this->cache[$lang] = $parsed;
        } else {
            throw new InvalidTranslationFileException("invalid translation file for: {$lang}");
        }
    }
}
