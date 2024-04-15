<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support\DataFile;

class JsonDataFile extends DataFile
{
    protected static ?string $extension = '.json';

    protected function parse($content): array
    {
        return (array) json_decode($content, true);
    }
}
