<?php

namespace App\Console\Commands;

use App\Queries\CUP;
use App\Queries\GitHub;
use Illuminate\Console\Command;

class CheckMods extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-mods';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks all mods for updates.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        (new CUP)->handle();

        foreach (config('mods.github') as $name => $mod) {
            (new GitHub)->handle($name, (object) $mod);
        }
    }
}
