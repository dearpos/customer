<?php

namespace Dearpos\Customer\Commands;

use Illuminate\Console\Command;

class CustomerCommand extends Command
{
    public $signature = 'customer';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
