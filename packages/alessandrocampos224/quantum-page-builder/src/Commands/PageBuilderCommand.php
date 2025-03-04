<?php

namespace Quantum\Page\Builder\PageBuilder\Commands;

use Illuminate\Console\Command;

class PageBuilderCommand extends Command
{
    public $signature = 'quantum-page-builder';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
