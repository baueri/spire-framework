<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Support;

use Baueri\Spire\Framework\Support\DataFile\PhpDataFile;

class Config extends PhpDataFile
{
    protected static ?string $basePath = 'config' . DS;
}
