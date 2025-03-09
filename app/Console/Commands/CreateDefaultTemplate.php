<?php

namespace App\Console\Commands;

use App\Models\Template;
use Illuminate\Console\Command;

class CreateDefaultTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-default-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Template::createDefault();

        echo "success";
        return 0;
    }
}
