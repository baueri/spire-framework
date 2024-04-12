<?php

declare(strict_types=1);

namespace Baueri\Spire\Framework\Console\BaseCommands;

use Baueri\Spire\Framework\Support\FileCache;
use Exception;
use Baueri\Spire\Framework\Console\Command;

class ClearCache extends Command
{
    public function __construct(
        private readonly FileCache $cache
    ) {
        parent::__construct();
    }

    public static function signature(): string
    {
        return 'cache:clear';
    }

    public static function description(): string
    {
        return 'Clears the cache directory';
    }

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        if (!$this->cache->clear()) {
            $this->output->error('Could not clear cache directory');
        }

        $this->output->success('Cache directory cleared');
    }
}
