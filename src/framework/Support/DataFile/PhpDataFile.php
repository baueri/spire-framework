<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support\DataFile;

class PhpDataFile extends DataFile
{
    protected static ?string $extension = 'php';

    protected function parse($content)
    {
        return $content;
    }

    protected static function getContent(string $filename)
    {
        return include $filename;
    }
}
