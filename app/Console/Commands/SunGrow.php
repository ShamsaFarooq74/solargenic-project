<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\HardwareAPIData\SunGrowController;

class SunGrow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sun:grow';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'SunGrow Cron Job Controller';

    protected $commands = [
        'App\Console\Commands\RegisteredUsers',
    ];

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
        $controller = new SunGrowController();
        $controller->sunGrow();
    }
}
