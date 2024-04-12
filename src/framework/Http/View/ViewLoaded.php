<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Http\View;

use Baueri\Spire\Framework\Event\Event;

class ViewLoaded extends Event
{
    protected static array $listeners = [];

    public string $filePath;

    public string $cachedFilePath;

    public function __construct(string $filePath, string $cachedFilePath)
    {
        $this->filePath = $filePath;
        $this->cachedFilePath = $cachedFilePath;
    }
}
